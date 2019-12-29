<?php

namespace alekciy\ofd\providers\taxcom\Model;

use alekciy\ofd\BaseModel;
use alekciy\ofd\interfaces\ShiftInterface;

/**
 * Сокращенная информация по смене.
 */
class ShiftShort extends BaseModel implements ShiftInterface
{
	/** @var string Заводской номер ФН */
	public $fnFactoryNumber = '';

	/** @var string Дата открытия */
	public $openDateTime = '';

	/** @var string Дата закрытия */
	public $closeDateTime = '';

	/** @var int Номер смены */
	public $shiftNumber = 0;

	/** @var int Кол-во чеков за смену */
	public $receiptCount = 0;

	/**
	 * @inheritDoc
	 */
	public function getRuleList(): array
	{
		return [
			'fnFactoryNumber' => ['required', ['lengthMin', 1], ['lengthMax', 16]],
			'openDateTime'    => ['required', ['dateFormat', 'Y-m-d\TH:i:s']],
			'shiftNumber'     => ['required', 'integer', ['min', 0]],
			'receiptCount'    => ['required', 'integer', ['min', 0]],

			'closeDateTime'   => [['dateFormat', 'Y-m-d\TH:i:s']],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getFnFactoryNumber(): string
	{
		return $this->fnFactoryNumber;
	}

	/**
	 * @inheritDoc
	 */
	public function getShiftNumber(): int
	{
		return $this->shiftNumber;
	}

	/**
	 * @inheritDoc
	 */
	public function getOpenDateTime(): string
	{
		return $this->openDateTime;
	}

	/**
	 * @inheritDoc
	 */
	public function getCloseDateTime(): string
	{
		return $this->closeDateTime;
	}
}