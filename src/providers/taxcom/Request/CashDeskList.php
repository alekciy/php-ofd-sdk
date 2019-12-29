<?php

namespace alekciy\ofd\providers\taxcom\Request;

use alekciy\ofd\providers\taxcom\RequestPage;
use alekciy\ofd\providers\taxcom\Status;

/**
 * Список касс (ККТ) для заданной торговой точки.
 */
final class CashDeskList extends RequestPage
{
	public $method = 'GET';
	public $path = '/API/v2/KKTList';

	/** @var string Идентификатор торговой точки */
	public $outletId = '';

	/** @var string Статус */
	public $status = '';

	/**
	 * @inheritDoc
	 */
	public function getPropertyMap(): array
	{
		return array_merge(parent::getPropertyMap(), [
			'outletId' => ['query' => 'id'],
			'status'   => ['query' => 'np'],
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function getRuleList(): array
	{
		return array_merge(parent::getRuleList(), [
			'outletId' => ['required', ['lengthMin', 1], ['lengthMax', 36]],
			'status' => [['in', [
				Status::OK,
				Status::PROBLEM,
				Status::WARNING,
			]]]
		]);
	}
}
