<?php

namespace alekciy\ofd\providers\yandex\Model;

use alekciy\ofd\BaseModel;
use alekciy\ofd\Converter;
use alekciy\ofd\interfaces\DocumentInterface;

class Document extends BaseModel implements DocumentInterface
{
	use Converter;

	/** @var int Номер смены */
	public $shiftNumber = 0;

	/** @var string Время создания */
	public $dateTime = '';

	/** @var int Полная сумма по чеку, копейки */
	public $totalSum = 0;

	/** @var int Сумма по чеку безнал, копейки */
	public $electronicSum = 0;

	/** @var int Сумма по чеку наличными, копейки */
	public $cashSum = 0;

	/** @var int Сумма по чеку с постоплатой (кредит), копейки */
	public $creditSum = 0;

	/** @var int Сумма по чеку с предоплатой (зачет аванса и или других платежей), копейки */
	public $prepaidSum = 0;

	/** @var int Сумма по чеку с оплатой встречным представлением (другой способ оплаты), копейки */
	public $provisionSum = 0;

	/** @var integer Номер за смену */
	public $numberInShift = 0;

	/** @var string Тип операции */
	public $accountingType = '';

	/** @var integer Номер фискального документа (ФД) */
	public $fdNumber = 0;

	/** @var string Заводской номер ФН */
	public $fnFactoryNumber;

	/** @var integer Тип документа */
	public $documentType = 0;

	/** @var string Регистрационный номер */
	public $kktRegNumber;

	/** @var integer Фискальный признак документа (ФПД) */
	public $fpd = '';

	// Типы
	const TYPE_OPEN                = 'openShift';              // отчет об открытии смены
	const TYPE_CLOSE               = 'closeShift';             // отчет о закрытии смены
	const TYPE_STATE               = 'currentStateReport';     // отчет о текущем состоянии расчетов
	const TYPE_CHECK               = 'receipt';                // кассовый чек
	const TYPE_CHECK_CORRECT       = 'receiptCorrection';      // кассовый чек коррекции
	const TYPE_STRICT              = 'bso';                    // бланк строгой отчетности (БСО)
	const TYPE_STRICT_CORRECT      = 'bsoCorrection';          // бланк строгой отчетности (БСО) коррекции
	const TYPE_REGISTRATION        = 'fiscalReport';           // отчет о регистрации
	const TYPE_REGISTRATION_CHANGE = 'fiscalReportCorrection'; // отчет об изменении параметров регистрации
	const TYPE_FN_CLOSE            = 'closeArchive';           // закрытие фискального накопителя (ФН)

	// Признак расчета
	const OPERATION_INCOME         = 'income';         // приход
	const OPERATION_INCOME_RETURN  = 'income_return';  // возврат прихода
	const OPERATION_OUTCOME        = 'outcome';        // расход
	const OPERATION_OUTCOME_RETURN = 'outcome_return'; // возврат расхода

	// Система налогообложения
	const TAX_OSN                    = 'osn';                       // общая система налогообложения (ОСН)
	const TAX_USN_INCOME             = 'usn_income';                // упрощенная система налогообложения (УСН), доход
	const TAX_USN_INCOME_EXPENDITURE = 'usn_income_wo_expenditure'; // упрощенная система налогообложения (УСН), доход-расход
	const TAX_AGRICULTURAL           = 'esn_agricultural';          // единый сельскохозяйственный (ЕСХН)
	const TAX_PATENT                 = 'psn';                       // патент

	/**
	 * @inheritDoc
	 */
	protected function getPropertyInitMap(): array
	{
		return [
			'shift'         => 'shiftNumber',
			'real_date'     => 'dateTime',
			'totalSum'      => ['totalSum', 'conv' => 'RubToKop'],
			'number'        => 'numberInShift',
			'operation'     => 'accountingType',
			'ecashTotalSum' => ['electronicSum', 'conv' => 'RubToKop'],
			'fn'            => 'fnFactoryNumber',
			'cashTotalSum'  => ['cashSum', 'conv' => 'RubToKop'],
			'creditSum'     => ['creditSum', 'conv' => 'RubToKop'],
			'code_name'     => 'documentType',
			'rn'            => 'kktRegNumber',
			'fiscalSign'    => 'fpd',
			'doc_number'    => 'fdNumber',
			'provisionSum'  => ['provisionSum', 'conv' => 'RubToKop'],
			'prepaidSum'    => ['prepaidSum', 'conv' => 'RubToKop'],
		];
	}

	/**
	 * @inheritDoc
	 */
	protected function getRuleList(): array
	{
		return [
			'fnFactoryNumber' => ['required', ['lengthMin', 1], ['lengthMax', 16]],
			'dateTime'        => ['required', ['dateFormat', 'Y-m-d H:i:s']],
			'fdNumber'        => ['required', 'integer'],
			'shiftNumber'     => ['required', 'integer', ['min', 0]],
			'numberInShift'   => ['required', 'integer', ['min', 0]],
			'fpd'             => ['required', ['regex', '~^[0-9]{9}$~u']],
			'kktRegNumber'    => ['required', ['lengthMin', 1], ['lengthMax', 16]],
			'documentType'    => ['required', ['in', [
				self::TYPE_OPEN,
				self::TYPE_CLOSE,
				self::TYPE_STATE,
				self::TYPE_CHECK,
				self::TYPE_CHECK_CORRECT,
				self::TYPE_STRICT,
				self::TYPE_STRICT_CORRECT,
				self::TYPE_REGISTRATION,
				self::TYPE_REGISTRATION_CHANGE,
				self::TYPE_FN_CLOSE,
			]]],

			'totalSum'       => ['integer', ['min', 0], ['max', PHP_INT_MAX]],
			'cashSum'        => ['integer', ['min', 0], ['max', PHP_INT_MAX]],
			'creditSum'      => ['integer', ['min', 0], ['max', PHP_INT_MAX]],
			'provisionSum'   => ['integer', ['min', 0], ['max', PHP_INT_MAX]],
			'prepaidSum'     => ['integer', ['min', 0], ['max', PHP_INT_MAX]],
			'electronicSum'  => ['integer', ['min', 0], ['max', PHP_INT_MAX]],
			'accountingType' => [['in', [
				self::OPERATION_INCOME,
				self::OPERATION_INCOME_RETURN,
				self::OPERATION_OUTCOME,
				self::OPERATION_OUTCOME_RETURN,
			]]],
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
	public function getNumber(): int
	{
		return $this->fdNumber;
	}

	/**
	 * @inheritDoc
	 */
	public function getCreatAt(): string
	{
		return $this->dateTime;
	}

	/**
	 * @inheritDoc
	 */
	public function getFpd(): string
	{
		return $this->fpd;
	}
}