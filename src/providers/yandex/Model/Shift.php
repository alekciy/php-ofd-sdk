<?php

namespace alekciy\ofd\providers\yandex\Model;

use alekciy\ofd\BaseModel;
use alekciy\ofd\interfaces\ShiftInterface;
use Exception;

class Shift extends BaseModel implements ShiftInterface
{
	/** @var string Дата открытия */
	public $openDateTime = '';

	/** @var string Дата закрытия */
	public $closeDateTime = '';

	/** @var string Регистрационный номер */
	public $kktRegNumber;

	/** @var string Заводской номер ФН */
	public $fnFactoryNumber;

	/** @var int Номер смены */
	public $shiftNumber = 0;

	/** @var OperationShiftReport Данные по операциям с типом «outcome» (расход) за смену */
	public $outcome;

	/** @var OperationShiftReport Данные по операциям с типом «outcome-return» (возврат расхода) за смену */
	public $outcomeReturn;

	/** @var OperationShiftReport Данные по операциям с типом «income-return» (возврат прихода) за смену */
	public $incomeReturn;

	/** @var OperationShiftReport Данные по операциям с типом расчета «income» (приход) за смену */
	public $income;

	/**
	 * @param array $operationShiftReportInit
	 * @return OperationShiftReport
	 * @throws Exception
	 */
	protected function getOperationShiftReport(array $operationShiftReportInit): OperationShiftReport
	{
		return new OperationShiftReport($operationShiftReportInit);
	}

	/**
	 * @inheritDoc
	 */
	protected function getPropertyInitMap(): array
	{
		return [
			'shift'          => 'shiftNumber',
			'fn'             => 'fnFactoryNumber',
			'rn'             => 'kktRegNumber',
			'open'           => 'openDateTime',
			'close'          => 'closeDateTime',
			'income'         => ['income', 'conv' => 'getOperationShiftReport'],
			'income_return'  => ['incomeReturn', 'conv' => 'getOperationShiftReport'],
			'outcome'        => ['outcome', 'conv' => 'getOperationShiftReport'],
			'outcome_return' => ['outcome_return', 'conv' => 'getOperationShiftReport'],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getRuleList(): array
	{
		return [
			'fnFactoryNumber' => ['required', ['lengthMin', 1], ['lengthMax', 16]],
			'shiftNumber'     => ['required', 'integer', ['min', 0]],
			'openDateTime'    => ['required', ['dateFormat', 'Y-m-d H:i:s']],
			'kktRegNumber'    => ['required', ['lengthMin', 1], ['lengthMax', 16]],

			'closeDateTime' => [['dateFormat', 'Y-m-d H:i:s']],
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