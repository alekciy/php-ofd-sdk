<?php

namespace alekciy\ofd\providers\yandex\Model;

use alekciy\ofd\BaseModel;
use alekciy\ofd\Converter;

/**
 * Отчет по смене.
 */
class OperationShiftReport extends BaseModel
{
	use Converter;

	/** @var integer Количество чеков с безналичной оплатой */
	public $electronicCount = 0;

	/** @var integer Сумма по чекам с безналичной оплатой, копейки */
	public $electronicSum = 0;

	/** @var integer Сумма по чекам с постоплатой (кредит), копейки */
	public $creditSum = 0;

	/** @var integer Количество чеков с постоплатой (кредит) */
	public $creditCount = 0;

	/** @var integer Количество чеков с оплатой наличными */
	public $cashCount = 0;

	/** @var integer Сумма по чекам с оплатой наличными, копейки */
	public $cashSum = 0;

	/** @var integer Сумма по чекам с предоплатой (зачет аванса и или других платежей), копейки */
	public $prepaidSum = 0;

	/** @var integer Количество чеков с предоплатой (зачет аванса и или других платежей) */
	public $prepaidCount = 0;

	/** @var integer Полная сумма по чекам, копейки */
	public $totalSum = 0;

	/** @var integer Общее количество чеков */
	public $totalCount = 0;

	/** @var integer Сумма по чекам с оплатой встречным представлением (другой способ оплаты), копейки */
	public $provisionSum = 0;

	/** @var integer Количество чеков с оплатой встречным представлением (другой способ оплаты) */
	public $provisionCount = 0;

	/**
	 * @inheritDoc
	 */
	protected function getPropertyInitMap(): array
	{
		return [
			'credit_sum'       => ['creditSum', 'conv' => 'RubToKop'],
			'ecashTotal_sum'   => ['electronicSum', 'conv' => 'RubToKop'],
			'total_sum'        => ['totalSum', 'conv' => 'RubToKop'],
			'prepaid_sum'      => ['prepaidSum', 'conv' => 'RubToKop'],
			'cashTotal_sum'    => ['cashSum', 'conv' => 'RubToKop'],
			'provision_sum'    => ['provisionSum', 'conv' => 'RubToKop'],
			'ecashTotal_count' => 'electronicCount',
			'cashTotal_count'  => 'cashCount',
			'prepaid_count'    => 'prepaidCount',
			'total_count'      => 'totalCount',
			'credit_count'     => 'creditCount',
			'provision_count'  => 'provisionCount',
		];
	}

	/**
	 * @inheritDoc
	 */
	protected function getRuleList(): array
	{
		return [
			'totalSum'        => ['integer', ['min', 0], ['max', PHP_INT_MAX]],
			'cashSum'         => ['integer', ['min', 0], ['max', PHP_INT_MAX]],
			'creditSum'       => ['integer', ['min', 0], ['max', PHP_INT_MAX]],
			'provisionSum'    => ['integer', ['min', 0], ['max', PHP_INT_MAX]],
			'prepaidSum'      => ['integer', ['min', 0], ['max', PHP_INT_MAX]],
			'electronicSum'   => ['integer', ['min', 0], ['max', PHP_INT_MAX]],
			'totalCount'      => ['integer', ['min', 0]],
			'cashCount'       => ['integer', ['min', 0]],
			'creditCount'     => ['integer', ['min', 0]],
			'provisionCount'  => ['integer', ['min', 0]],
			'prepaidCount'    => ['integer', ['min', 0]],
			'electronicCount' => ['integer', ['min', 0]],
		];
	}
}