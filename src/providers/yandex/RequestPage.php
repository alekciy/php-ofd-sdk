<?php

namespace alekciy\ofd\providers\yandex;

/**
 * Класс запроса к провайдеру для запросов с постраничной навигацией.
 */
class RequestPage extends \alekciy\ofd\RequestPage
{
	/** @var int Номер страницы (нумерация от 1) */
	public $pageNumber = 1;

	/** @var int Элементов на странице */
	public $perPage = 100;

	/**
	 * @inheritDoc
	 */
	public function getPropertyMap(): array
	{
		return [
			'pageNumber' => ['query' => 'offset'],
			'perPage'    => ['query' => 'limit'],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getRuleList(): array
	{
		return [
			'pageNumber' => ['integer', ['min', 1]],
			'perPage'    => ['integer', ['min', 1], ['max', 100]],
		];
	}
}