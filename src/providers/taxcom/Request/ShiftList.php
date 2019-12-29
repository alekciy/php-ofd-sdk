<?php

namespace alekciy\ofd\providers\taxcom\Request;

use alekciy\ofd\providers\taxcom\RequestPage;

/**
 * Список смен заданной ККТ.
 */
class ShiftList extends RequestPage
{
	public $method = 'GET';
	public $path = '/API/v2/ShiftList';

	/** @var string Заводской номер фискального накопителя (ФН) */
	public $fnFactoryNumber = '';

	/** @var string Начало периода */
	public $start = '';

	/** @var string Окончание периода */
	public $end = '';

	/**
	 * @inheritDoc
	 */
	public function getPropertyMap(): array
	{
		return array_merge(parent::getPropertyMap(), [
			'fnFactoryNumber' => ['query' => 'fn'],
			'start'           => ['query' => 'begin'],
			'end'             => ['query' => 'end'],
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function getRuleList(): array
	{
		return array_merge(parent::getRuleList(), [
			'fnFactoryNumber' => ['required', ['lengthMin', 1], ['lengthMax', 16]],
			'start'           => ['required', ['dateFormat', 'Y-m-d\TH:i:s']],
			'end'             => ['required', ['dateFormat', 'Y-m-d\TH:i:s']],
		]);
	}
}