<?php

namespace alekciy\ofd\providers\taxcom\Model;

use alekciy\ofd\BaseModel;
use alekciy\ofd\interfaces\ShiftInterface;

/**
 * Информация по смене.
 */
class Shift extends BaseModel implements ShiftInterface
{
	/** @var string Кассир */
	public $cashier = '';

	/** @var integer Номер ФД отчета об открытии смены  */
	public $openFdNumber = 0;

	/** @var integer Номер ФД отчета о закрытии смены */
	public $closeFdNumber = 0;

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

	/** @var string  */
	public $state = '';

	// Статус
	const STATUS_OPEN   = 'Open';
	const STATUS_CLOSE  = 'Close';

	/**
	 * @inheritDoc
	 */
	public function getRuleList(): array
	{
		return [
			'fnFactoryNumber' => ['required', ['lengthMin', 1], ['lengthMax', 16]],
			'shiftNumber'     => ['required', 'integer', ['min', 0]],
			'openFdNumber'    => ['required', 'integer', ['min', 0]],
			'cashier'         => ['required', ['lengthMin', 1], ['lengthMax', 256]],
			'openDateTime'    => ['required', ['dateFormat', 'Y-m-d\TH:i:s']],
			'state'           => ['required', ['in', [
				self::STATUS_CLOSE,
				self::STATUS_OPEN,
			]]],

			'closeFdNumber' => ['integer', ['min', 0]],
			'receiptCount'  => ['integer', ['min', 0]],
			'closeDateTime' => [['dateFormat', 'Y-m-d\TH:i:s']],
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