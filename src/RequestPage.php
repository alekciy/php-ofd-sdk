<?php

namespace alekciy\ofd;

/**
 * Класс запроса к провайдеру для запросов с постраничной навигацией.
 */
abstract class RequestPage extends Request
{
	/** @var int Номер страницы (нумерация от 1) */
	public $pageNumber = 1;

	/** @var int Элементов на странице */
	public $perPage = 100;
}