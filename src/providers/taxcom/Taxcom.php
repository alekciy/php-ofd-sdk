<?php

namespace alekciy\ofd\providers\taxcom;

use alekciy\ofd\interfaces\CashDeskInterface;
use alekciy\ofd\interfaces\OutletInterface;
use alekciy\ofd\interfaces\ShiftInterface;
use alekciy\ofd\interfaces\ProviderInterface;
use alekciy\ofd\providers\taxcom\Model\CashDeskShort;
use alekciy\ofd\providers\taxcom\Model\Document;
use alekciy\ofd\providers\taxcom\Model\OutletShort;
use alekciy\ofd\providers\taxcom\Model\ShiftShort;
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
}