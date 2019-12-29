<?php

namespace alekciy\ofd\providers\taxcom\Model;

use alekciy\ofd\BaseModel;

class Document extends BaseModel
{
	/** @var string Признак расчета */
	public $accountingType = '';

	/** @var int Сумма нал (копейки) */
	public $cash = 0;

	/** @var string Кассир */
	public $cashier = '';

	/** @var string Время создания */
	public $dateTime = '';

	/** @var integer Тип документа */
	public $documentType = 0;

	/** @var int Сумма безнал (копейки) */
	public $electronic = 0;

	/** @var integer Номер фискального документа (ФД) */
	public $fdNumber = 0;

	/** @var string Заводской номер ФН */
	public $fnFactoryNumber = '';

	/** @var string Фискальный признак документа */
	public $fpd = '';

	/** @var int Сумма с НДС 0% (копейки) */
	public $nds0Sum = 0;

	/** @var int НДС 10% (копейки) */
	public $nds10 = 0;

	/** @var int НДС 18% (копейки) */
	public $nds18 = 0;

	/** @var int НДС 20% (копейки) */
	public $nds20 = 0;

	/** @var int НДС 10/110 (копейки) */
	public $ndsC10 = 0;

	/** @var int НДС 18/118 (копейки) */
	public $ndsC18 = 0;

	/** @var int НДС 20/120 (копейки) */
	public $ndsC20 = 0;

	/** @var int Сумма без НДС (копейки) */
	public $nondsSum = 0;

	/** @var integer Номер за смену */
	public $numberInShift = 0;

	/** @var int Номер смены */
	public $shiftNumber = 0;

	/** @var int Сумма (копейки) */
	public $sum = 0;

	/** @var string Система налогообложения */
	public $taxationSystem = '';

	// Система налогообложения
	const TAX_OSN                    = 'OSN';                  // общая система налогообложения (ОСН)
	const TAX_USN_INCOME             = 'USNIncome';            // упрощенная система налогообложения (УСН), доход
	const TAX_USN_INCOME_EXPENDITURE = 'USNIncomeExpenditure'; // упрощенная система налогообложения (УСН), доход-расход
	const TAX_ENVD                   = 'ENVD';                 // единый налог на вмененный доход (ЕНВД)
	const TAX_ESN                    = 'ESN';                  // единый социальный налог (ЕСН)
	const TAX_PATENT                 = 'Patent';               // Патент

	// Типы
	const TYPE_OPEN           = 2;  // отчет об открытии смены
	const TYPE_CLOSE          = 5;  // отчет о закрытии смены
	const TYPE_STATE          = 21; // отчет о текущем состоянии расчетов
	const TYPE_CHECK          = 3;  // кассовый чек
	const TYPE_CHECK_CORRECT  = 31; // кассовый чек коррекции
	const TYPE_STRICT         = 4;  // бланк строгой отчетности
	const TYPE_STRICT_CORRECT = 41; // бланк строгой отчетности коррекции

	// Признак расчета
	const ACCOUNTING_INCOME             = 'Income';            // приход
	const ACCOUNTING_INCOME_RETURN      = 'IncomeReturn';      // возврат прихода
	const ACCOUNTING_EXPENDITURE        = 'Expenditure';       // расход
	const ACCOUNTING_EXPENDITURE_RETURN = 'ExpenditureReturn'; // возврат расхода

	/**
	 * @inheritDoc
	 */
	protected function getRuleList(): array
	{
		return [
			'fnFactoryNumber' => ['required', ['lengthMin', 1], ['lengthMax', 16]],
			'dateTime'        => ['required', ['dateFormat', 'Y-m-d\TH:i:s']],
			'fdNumber'        => ['required', 'integer'],
			'fpd'             => ['required', ['lengthMin', 1], ['lengthMax', 10]],
			'documentType'    => ['required', ['in', [
				self::TYPE_OPEN,
				self::TYPE_CLOSE,
				self::TYPE_STATE,
				self::TYPE_CHECK,
				self::TYPE_CHECK_CORRECT,
				self::TYPE_STRICT,
				self::TYPE_STRICT_CORRECT,
			]]],

			'cashier'    => [['lengthMin', 1], ['lengthMax', 256]],
			'sum'        => ['integer', ['min', 0], ['max', PHP_INT_MAX ]],
			'cash'       => ['integer', ['min', 0], ['max', PHP_INT_MAX ]],
			'electronic' => ['integer', ['min', 0], ['max', PHP_INT_MAX ]],
			'nondsSum'   => ['integer', ['min', 0], ['max', PHP_INT_MAX ]],
			'nds0Sum'    => ['integer', ['min', 0], ['max', PHP_INT_MAX ]],
			'nds10'      => ['integer', ['min', 0]],
			'nds18'      => ['integer', ['min', 0]],
			'nds20'      => ['integer', ['min', 0]],
			'ndsC10'     => ['integer', ['min', 0]],
			'ndsC18'     => ['integer', ['min', 0]],
			'ndsC20'     => ['integer', ['min', 0]],
			'accountingType' => [['in', [
				self::ACCOUNTING_INCOME,
				self::ACCOUNTING_INCOME_RETURN,
				self::ACCOUNTING_EXPENDITURE,
				self::ACCOUNTING_EXPENDITURE_RETURN,
			]]],
		];
	}
}