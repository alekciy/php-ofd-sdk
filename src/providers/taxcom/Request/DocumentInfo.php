<?php

namespace alekciy\ofd\providers\taxcom\Request;

use alekciy\ofd\Request;

/**
 * Информация по фискальному документу (ФД) в тегах.
 */
class DocumentInfo extends Request
{
	public $method = 'GET';
	protected $path = '/API/v2/DocumentInfo';

	/** @var string Заводской номер ФН */
	public $fnFactoryNumber = '';

	/** @var integer Номер фискального документа (ФД) */
	public $fdNumber = 0;

	/**
	 * @inheritDoc
	 */
	public function getPropertyMap(): array
	{
		return [
			'fnFactoryNumber' => ['query' => 'fn'],
			'fdNumber'        => ['query' => 'fd'],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getRuleList(): array
	{
		return [
			'fnFactoryNumber' => ['required', ['lengthMin', 1], ['lengthMax', 16]],
			'fdNumber'        => ['required', 'integer'],
		];
	}
}