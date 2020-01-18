<?php

namespace alekciy\ofd\providers\yandex\Request;

use alekciy\ofd\providers\yandex\RequestPage;

/**
 * Список торговых точек.
 */
final class OutletList extends RequestPage
{
	public $method = 'GET';
	protected $path = '/v1/retail_points';

	/**
	 * @inheritDoc
	 */
	public function getPropertyMap(): array
	{
		return array_merge(parent::getPropertyMap(), [
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function getRuleList(): array
	{
		return array_merge(parent::getRuleList(), [
		]);
	}
}
