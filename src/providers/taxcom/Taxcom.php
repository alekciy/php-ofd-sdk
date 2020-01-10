<?php

namespace alekciy\ofd\providers\taxcom;

use alekciy\ffd\DocumentInterface as ffdDocument;
use alekciy\ffd\documents\Check;
use alekciy\ffd\documents\Close;
use alekciy\ffd\documents\Confirmation;
use alekciy\ffd\documents\FnClose;
use alekciy\ffd\documents\Open;
use alekciy\ffd\documents\Registration;
use alekciy\ffd\documents\RegistrationChange;
use alekciy\ffd\documents\State;
use alekciy\ffd\documents\Strict;
use alekciy\ffd\documents\StrictCorrect;
use alekciy\ffd\tags\Tag1012;
use alekciy\ofd\interfaces\CashDeskInterface;
use alekciy\ofd\interfaces\DocumentInterface;
use alekciy\ofd\interfaces\OutletInterface;
use alekciy\ofd\interfaces\ShiftInterface;
use alekciy\ofd\interfaces\ProviderInterface;
use alekciy\ofd\providers\taxcom\Model\CashDeskShort;
use alekciy\ofd\providers\taxcom\Model\Document;
use alekciy\ofd\providers\taxcom\Model\OutletShort;
use alekciy\ofd\providers\taxcom\Model\ShiftShort;
use alekciy\ofd\providers\taxcom\Request\DocumentInfo;
use GuzzleHttp\Exception\GuzzleException;
use DateTime;
use alekciy\ofd\providers\taxcom\Model\CashDesk;
use alekciy\ofd\providers\taxcom\Request\DocumentList;
use alekciy\ofd\providers\taxcom\Request\CashDeskList;
use alekciy\ofd\providers\taxcom\Request\OutletList;
use alekciy\ofd\providers\taxcom\Request\ShiftList;
use Exception;
use DateTimeZone;
use ReflectionException;

/**
 * @see https://lk-ofd.taxcom.ru/ApiHelp/
 */
class Taxcom implements ProviderInterface
{
	/** @var Client */
	protected $client = null;

	/**
	 * @param Client $client
	 */
	public function __construct(Client $client)
	{
		$this->client = $client;
	}

	/**
	 * Для запросов с постраничной навигацией загрузит все возможные элементы.
	 *
	 * @param RequestPage $endpoint
	 * @return array
	 * @throws Exception
	 * @throws GuzzleException
	 */
	private function getAllItemList(RequestPage $endpoint): array
	{
		$result = [];
		do {
			$response = $this->client->request($endpoint);
			foreach ($response['records'] as $record) {
				$result[] = $record;
			}

			$lastPageNumber = ceil($response['counts']['recordFilteredCount'] / $endpoint->perPage);
			++$endpoint->pageNumber;
		} while ($endpoint->pageNumber <= $lastPageNumber);
		return $result;
	}

	/**
	 * @inheritDoc
	 *
	 * @return OutletInterface[]
	 * @throws GuzzleException
	 * @throws ReflectionException
	 * @throws Exception
	 */
	public function getOutletList(): array
	{
		$result = [];
		$responseOutletList = $this->getAllItemList(new OutletList([]));
		foreach ($responseOutletList as $responseOutlet) {
			$outletShort = new OutletShort($responseOutlet);
			$result[$outletShort->id] = $outletShort;
		}
		return $result;
	}

	/**
	 * @inheritDoc
	 *
	 * @throws GuzzleException
	 * @throws Exception
	 */
	public function getCashDeskList(OutletInterface $outlet = null): array
	{
		$result = [];
		$outletList = $outlet instanceof OutletInterface
			? [$outlet]
			: $this->getOutletList();
		foreach ($outletList as $outlet) {
			$responseCashDeskList = $this->getAllItemList(new CashDeskList([
				'outletId' => $outlet->getId(),
			]));
			foreach ($responseCashDeskList as $responseCashDesk) {
				$result[] = new CashDeskShort($responseCashDesk);
			}
		}
		return $result;
	}

	/**
	 * @inheritDoc
	 *
	 * @throws Exception
	 * @throws ReflectionException
	 * @throws GuzzleException
	 */
	public function getShiftList(CashDeskInterface $cashDesk = null, DateTime $start = null, DateTime $end = null): array
	{
		$result = [];

		// Задаем время
		if (!$start instanceof DateTime) {
			$start = new DateTime('today');
		}
		if (!$end instanceof DateTime) {
			$end = new DateTime('tomorrow');
		}
		// Сервис оперирует часовым поясом +0, поэтому корректируем
		$tz = new DateTimeZone('UTC');
		$start->setTimezone($tz);
		$end->setTimezone($tz);

		$cashDeskList = $cashDesk instanceof CashDesk
			? [$cashDesk]
			: $this->getCashDeskList();
		foreach ($cashDeskList as $cashDesk) {
			$responseShiftList = $this->getAllItemList(new ShiftList([
				'fnFactoryNumber' => $cashDesk->getFnFactoryNumber(),
				'start'           => $start->format('Y-m-d\TH:i:s'),
				'end'             => $end->format('Y-m-d\TH:i:s'),
			]));
			foreach ($responseShiftList as $responseShift) {
				$result[] = new ShiftShort($responseShift);
			}
		}
		return $result;
	}

	/**
	 * @inheritDoc
	 *
	 * @throws Exception
	 * @throws ReflectionException
	 * @throws GuzzleException
	 */
	public function getDocumentList(ShiftInterface $shift = null, DateTime $start = null, DateTime $end = null): array
	{
		$result = [];

		// Задаем время
		if (!$start instanceof DateTime) {
			$start = new DateTime('today');
		}
		if (!$end instanceof DateTime) {
			$end = new DateTime('tomorrow');
		}
		// Сервис оперирует часовым поясом +0, поэтому корректируем
		$tz = new DateTimeZone('UTC');
		$start->setTimezone($tz);
		$end->setTimezone($tz);

		$shiftList = $shift instanceof ShiftInterface
			? [$shift]
			: $this->getShiftList(null, $start, $end);
		foreach ($shiftList as $shift) {
			$responseDocumentList = $this->getAllItemList(new DocumentList([
				'fnFactoryNumber' => $shift->getFnFactoryNumber(),
				'shiftNumber'     => $shift->getShiftNumber(),
			]));
			foreach ($responseDocumentList as $responseDocument) {
				$result[] = new Document($responseDocument);
			}
		}
		return $result;
	}

	/**
	 * @param Document $document
	 * @return Registration|Open|Check|Strict|Close|FnClose|Confirmation|RegistrationChange|State|StrictCorrect
	 * @throws GuzzleException
	 * @throws ReflectionException
	 * @throws Exception
	 */
	public function getDocumentTag(Document $document): ffdDocument
	{
		$documentTagList = $this->getDocumentTagList($document);
		return reset($documentTagList);
	}

	/**
	 * Получить список тегов (реквизитов) документа(-ов) за заданный период. По умолчанию за сегодня.
	 *
	 * @param Document[]|Document $document
	 * @param DateTime|null $start
	 * @param DateTime|null $end
	 * @return Registration[]|Open[]|Check[]|Strict[]|Close[]|FnClose[]|Confirmation[]|RegistrationChange[]|State[]|StrictCorrect[]
	 * @throws GuzzleException
	 * @throws ReflectionException
	 * @throws Exception
	 */
	public function getDocumentTagList($document = null, DateTime $start = null, DateTime $end = null): array
	{
		$result = [];

		// Задаем время
		if (!$start instanceof DateTime) {
			$start = new DateTime('today');
		}
		if (!$end instanceof DateTime) {
			$end = new DateTime('tomorrow');
		}
		// Сервис оперирует часовым поясом +0, поэтому корректируем
		$tz = new DateTimeZone('UTC');
		$start->setTimezone($tz);
		$end->setTimezone($tz);

		$documentList = $document instanceof DocumentInterface
			? [$document]
			: $this->getDocumentList(null, $start, $end);
		foreach ($documentList as $document) {
			$responseDocumentInfo = $this->client->request(new DocumentInfo([
				'fnFactoryNumber' => $document->getFnFactoryNumber(),
				'fdNumber'        => $document->getNumber(),
			]));

			$tagList = $responseDocumentInfo['document'];
			$version = $responseDocumentInfo['documentFormatVersion'];
			$format = ffdDocument::FORMAT_PRINT;

			$tagList[Tag1012::getCode()] = (new DateTime($tagList[Tag1012::getCode()], $tz))->format('d.m.y H:i');

			switch ($responseDocumentInfo['documentType']) {
				case ffdDocument::TYPE_REGISTRATION:
					$result[] = new Registration($version, $format, $tagList);
					break;
				case ffdDocument::TYPE_OPEN:
					$result[] = new Open($version, $format, $tagList);
					break;
				case ffdDocument::TYPE_CHECK:
					$result[] = new Check($version, $format, $tagList);
					break;
				case ffdDocument::TYPE_STRICT:
					$result[] = new Strict($version, $format, $tagList);
					break;
				case ffdDocument::TYPE_CLOSE:
					$result[] = new Close($version, $format, $tagList);
					break;
				case ffdDocument::TYPE_FN_CLOSE:
					$result[] = new FnClose($version, $format, $tagList);
					break;
				case ffdDocument::TYPE_CONFIRMATION:
					$result[] = new Confirmation($version, $format, $tagList);
					break;
				case ffdDocument::TYPE_REGISTRATION_CHANGE:
					$result[] = new RegistrationChange($version, $format, $tagList);
					break;
				case ffdDocument::TYPE_STATE:
					$result[] = new State($version, $format, $tagList);
					break;
				case ffdDocument::TYPE_STRICT_CORRECT:
					$result[] = new StrictCorrect($version, $format, $tagList);
					break;
				default:
					throw new Exception('Неизвестный тип документа');
			}
		}
		return $result;
	}
}