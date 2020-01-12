<?php

namespace alekciy\ofd\providers\yandex\Request;

use alekciy\ofd\providers\yandex\RequestPage;

/**
 * Список торговых точек.
 */
final class CashDeskList extends RequestPage
{
	public $method = 'GET';
	public $path = '/v1/cashboxes';

	/** @var integer */
	public $outletId;

	/**
	 * @inheritDoc
	 */
	public function getPropertyMap(): array
	{
		return array_merge(parent::getPropertyMap(), [
			'outletId' => ['query' => 'retail_point_id'],
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function getRuleList(): array
	{
		return array_merge(parent::getRuleList(), [
			'outletId' => ['numeric'],
		]);
	}
}
