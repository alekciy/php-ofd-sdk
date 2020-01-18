<?php

namespace alekciy\ofd\providers\taxcom\Model;

use alekciy\ofd\BaseModel;
use alekciy\ofd\interfaces\CashDeskInterface;
use alekciy\ofd\providers\taxcom\Status;

/**
 * Сокращенная информация по кассе.
 */
class CashDeskShort extends BaseModel implements CashDeskInterface
{
	/** @var string Состояние  */
	public $cashdeskState;

	/** @var string Номер фискального регистратора (ФН) */
	public $fnFactoryNumber;

	/** @var string Серийный (заводской) номер кассы */
	public $kktFactoryNumber;

	/** @var string Регистрационный номер кассы (полученный в ФНС) */
	public $kktRegNumber;

	/** @var string Название */
	public $name;

	/** @var string Признак наличия проблемы */
	public $problemIndicator = '';

	/**
	 * @inheritDoc
	 */
	public function getRuleList(): array
	{
		return [
			'name' => ['required', ['lengthMax', 255]],
			'kktRegNumber' => ['required', ['lengthMin', 1], ['lengthMax', 16]],
			'kktFactoryNumber' => ['required', ['lengthMin', 1], ['lengthMax', 20]],
			'fnFactoryNumber' => ['required', ['lengthMin', 1], ['lengthMax', 16]],
			'problemIndicator' => ['required', ['in', [
				Status::OK,
				Status::PROBLEM,
				Status::WARNING,
			]]],
			'cashdeskState' => ['required', ['in', [
				CashDesk::STATUS_ACTIVE,
				CashDesk::STATUS_EXPIRES,
				CashDesk::STATUS_EXPIRED,
				CashDesk::STATUS_INACTIVE,
				CashDesk::STATUS_ACTIVATION,
				CashDesk::STATUS_DEACTIVATION,
				CashDesk::STATUS_FN_CHANGE,
				CashDesk::STATUS_FN_REGISTRATION,
				CashDesk::STATUS_FN_REGISTRATION_ERROR,
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
	public function getKktFactoryNumber(): string
	{
		return $this->kktFactoryNumber;
	}

	/**
	 * @inheritDoc
	 */
	public function getKktRegNumber(): string
	{
		return $this->kktRegNumber;
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @inheritDoc
	 */
	protected function getPropertyInitMap(): array
	{
		return [];
	}
}