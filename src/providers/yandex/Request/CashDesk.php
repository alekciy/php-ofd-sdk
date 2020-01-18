<?php

namespace alekciy\ofd\providers\yandex\Request;

use alekciy\ofd\providers\yandex\RequestPage;

/**
 * Подробная информацию о кассе.
 */
final class CashDesk extends RequestPage
{
	public $method = 'GET';
	protected $path = '/v1/cashboxes/';

	/** @var integer */
	public $id;

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
			'id' => ['required', 'numeric'],
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function getPath(): string
	{
		return $this->path . $this->id;
	}
}
