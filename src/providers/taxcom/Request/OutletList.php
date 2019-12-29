<?php

namespace alekciy\ofd\providers\taxcom\Request;

use alekciy\ofd\providers\taxcom\RequestPage;
use alekciy\ofd\providers\taxcom\Status;

/**
 * Список торговых точек.
 */
final class OutletList extends RequestPage
{
	public $method = 'GET';
	public $path = '/API/v2/OutletList';

	/** @var string Идентификатор */
	public $id = '';

	/** @var string Статус */
	public $status = '';

	/**
	 * @inheritDoc
	 */
	public function getPropertyMap(): array
	{
		return array_merge(parent::getPropertyMap(), [
			'id'     => ['query' => 'id'],
			'status' => ['query' => 'np'],
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function getRuleList(): array
	{
		return array_merge(parent::getRuleList(), [
			'id' => [['lengthMin', 1], ['lengthMax', 36]],
			'status' => [['in', [
				Status::OK,
				Status::PROBLEM,
				Status::WARNING,
			]]]
		]);
	}
}
