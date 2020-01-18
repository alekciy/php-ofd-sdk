<?php

namespace alekciy\ofd\providers\yandex\Model;

use alekciy\ofd\BaseModel;
use alekciy\ofd\interfaces\OutletInterface;

/**
 * Детальная информация по торговой точке
 */
class Outlet extends BaseModel implements OutletInterface
{
	/** @var string Адрес торговой точки */
	public $address = '';

	/** @var integer Идентификатор торговой точки */
	public $id = 0;

	/** @var integer Идентификатор компании */
	public $companyId = 0;

	/** @var integer Идентификатор клиента, зарегистрировавшего компанию в системе */
	public $clientId = 0;

	/** @var string Название торговой точки */
	public $name = '';

	/**
	 * @inheritDoc
	 */
	public function getRuleList(): array
	{
		return [
			'id'      => ['required', 'numeric'],
			'name'    => ['required'],
			'address' => ['required'],

			'companyId' => ['numeric'],
			'clientId'  => ['numeric'],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string
	{
		return strval($this->id);
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
	public function getAddress(): string
	{
		return $this->address;
	}

	/**
	 * @inheritDoc
	 */
	protected function getPropertyInitMap(): array
	{
		return [];
	}
}