<?php

namespace alekciy\ofd\providers\yandex;

use alekciy\ofd\interfaces\CashDeskInterface;
use alekciy\ofd\interfaces\OutletInterface;
use alekciy\ofd\interfaces\ProviderInterface;
use alekciy\ofd\interfaces\ShiftInterface;
use alekciy\ofd\providers\yandex\Model\CashDesk;
use alekciy\ofd\providers\yandex\Model\Document;
use alekciy\ofd\providers\yandex\Model\Outlet;
use alekciy\ofd\providers\yandex\Model\Shift;
use alekciy\ofd\providers\yandex\Request\CashDeskList;
use alekciy\ofd\providers\yandex\Request\DocumentList;
use alekciy\ofd\providers\yandex\Request\OutletList;
use alekciy\ofd\providers\yandex\Request\ShiftList;
use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;

/**
 * @see https://yandex.ru/dev/ofd/doc/dg/concepts/about-docpage/
 */
class Yandex implements ProviderInterface
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
			$recordList = isset($response['data'])
				? $response['data']
				: $response;
			foreach ($recordList as $record) {
				$result[] = $record;
			}
			++$endpoint->pageNumber;
		} while (!empty($response));
		return $result;
	}

	/**
	 * @inheritDoc
	 *
	 * @return OutletInterface[]
	 * @throws GuzzleException
	 * @throws Exception
	 */
	public function getOutletList(): array
	{
		$result = [];
		$responseOutletList = $this->getAllItemList(new OutletList([]));
		foreach ($responseOutletList as $responseOutlet) {
			$outlet = new Outlet($responseOutlet);
			$result[$outlet->getId()] = $outlet;
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
				$result[] = new CashDesk($responseCashDesk);
			}
		}
		return $result;
	}

	/**
	 * Информация по кассе.
	 *
	 * @param int $id
	 * @return CashDesk
	 * @throws GuzzleException
	 * @throws Exception
	 */
	public function getCashDesk(int $id): CashDesk
	{
		$responseCashDesk = $this->client->request(new Request\CashDesk(['id' => $id]));
		return new CashDesk($responseCashDesk);
	}

	/**
	 * @inheritDoc
	 *
	 * @throws Exception
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

		$cashDeskList = $cashDesk instanceof CashDeskInterface
			? [$cashDesk]
			: $this->getCashDeskList();
		$responseShiftList = $this->getAllItemList(new ShiftList([
			'cashBoxIdList' => array_column($cashDeskList, 'id'),
			'start'         => $start->format('Y-m-d H:i:s'),
			'end'           => $end->format('Y-m-d H:i:s'),
		]));

		// Получаем результат
		foreach ($responseShiftList as $responseShift) {
			$result[] = new Shift($responseShift);
		}
		return $result;
	}

	/**
	 * @inheritDoc
	 *
	 * @throws Exception
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

		$shiftList = $shift instanceof ShiftInterface
			? [$shift]
			: $this->getShiftList(null, $start, $end);
		foreach ($shiftList as $shift) {
			$responseDocumentList = $this->getAllItemList(new DocumentList([
				'shiftNumber' => $shift->getShiftNumber(),
				'start'       => $start->format('Y-m-d H:i:s'),
				'end'         => $end->format('Y-m-d H:i:s'),
			]));
			foreach ($responseDocumentList as $responseDocument) {
				$result[] = new Document($responseDocument);
			}
		}
		return $result;
	}
}