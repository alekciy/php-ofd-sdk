<?php

namespace alekciy\ofd\providers\yandex;

use alekciy\ofd\interfaces\CashDeskInterface;
use alekciy\ofd\interfaces\OutletInterface;
use alekciy\ofd\interfaces\ProviderInterface;
use alekciy\ofd\interfaces\ShiftInterface;
use alekciy\ofd\providers\yandex\Request\OutletList;
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
		$response = $this->client->request($endpoint);
		$result = [];
		// TODO
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
		// TODO
		return $result;
	}

	/**
	 * @inheritDoc
	 */
	public function getCashDeskList(OutletInterface $outlet = null): array
	{
		// TODO: Implement getCashDeskList() method.
	}

	/**
	 * @inheritDoc
	 */
	public function getShiftList(CashDeskInterface $cashDesk = null, DateTime $start = null, DateTime $end = null): array
	{
		// TODO: Implement getShiftList() method.
	}

	/**
	 * @inheritDoc
	 */
	public function getDocumentList(ShiftInterface $shift = null, DateTime $start = null, DateTime $end = null): array
	{
		// TODO: Implement getDocumentList() method.
	}
}