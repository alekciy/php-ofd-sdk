<?php

namespace alekciy\ofd\providers\taxcom\Request;

use alekciy\ofd\providers\taxcom\Model\Document;
use alekciy\ofd\providers\taxcom\RequestPage;

/**
 * Список документов по смене.
 */
class DocumentList extends RequestPage
{
	public $method = 'GET';
	protected $path = '/API/v2/DocumentList';

	/** @var string Заводской номер ФН */
	public $fnFactoryNumber = '';

	/** @var int Номер смены */
	public $shiftNumber = 0;

	/** @var integer[] Тип документа */
	public $type = [];

	/**
	 * @inheritDoc
	 */
	public function getPropertyMap(): array
	{
		return array_merge(parent::getPropertyMap(), [
			'fnFactoryNumber' => ['query' => 'fn'],
			'shiftNumber'     => ['query' => 'shift'],
			'type'            => ['query' => 'type'],
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function getRuleList(): array
	{
		return array_merge(parent::getRuleList(), [
			'fnFactoryNumber' => ['required', ['lengthMin', 1], ['lengthMax', 16]],
			'shiftNumber'     => ['required', 'integer', ['min', 0]],
			'type'            => ['array', ['subset', [
				Document::TYPE_CHECK,
				Document::TYPE_CLOSE,
				Document::TYPE_STATE,
				Document::TYPE_CHECK_CORRECT,
				Document::TYPE_STRICT,
				Document::TYPE_STRICT_CORRECT,
				Document::TYPE_OPEN,
			]]],
		]);
	}
}