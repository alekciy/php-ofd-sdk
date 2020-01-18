<?php

namespace alekciy\ofd\providers\yandex\Model;

use alekciy\ofd\BaseModel;
use alekciy\ofd\interfaces\CashDeskInterface;

class CashDesk extends BaseModel implements CashDeskInterface
{
	/**
	 * @inheritDoc
	 */
	protected function getPropertyInitMap(): array
	{
		return [
			'retail_point_id' => 'outletId',
			'sn'              => 'kktFactoryNumber',
			'fiscal_drive_sn' => 'fnFactoryNumber',
			'rn'              => 'kktRegNumber',
			'paid_at'         => 'cashDeskEndDateTime',
		];
	}

	/** @var integer Идентификатор компании */
	public $companyId;

	/** @var integer */
	public $outletId;

	/** @var string Заводской номер */
	public $kktFactoryNumber;

	/** @var string Заводской номер ФН */
	public $fnFactoryNumber;

	/** @var integer */
	public $id;

	/** @var string Регистрационный номер */
	public $kktRegNumber;

	/** @var string Оплачена по */
	public $cashDeskEndDateTime;

	/**
	 * @inheritDoc
	 */
	protected function getRuleList(): array
	{
		return [
			'kktFactoryNumber' => ['required', ['lengthMin', 1], ['lengthMax', 20]],
			'fnFactoryNumber'  => ['required', ['lengthMin', 1], ['lengthMax', 16]],

			'kktRegNumber'        => [['lengthMin', 1], ['lengthMax', 16]],
			'cashDeskEndDateTime' => [['dateFormat', 'H-m-dTH:i:s']],
			'id'                  => ['numeric'],
			'outletId'            => ['numeric'],
			'companyId'           => ['numeric'],
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
		return $this->kktRegNumber ?? '';
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): string
	{
		return $this->kktFactoryNumber;
	}
}