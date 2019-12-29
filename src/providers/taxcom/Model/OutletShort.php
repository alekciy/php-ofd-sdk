<?php

namespace alekciy\ofd\providers\taxcom\Model;

use alekciy\ofd\BaseModel;
use alekciy\ofd\interfaces\OutletInterface;
use alekciy\ofd\providers\taxcom\Status;

/**
 * Сокращенная информацию по торговой точке.
 */
class OutletShort extends BaseModel implements OutletInterface
{
	/** @var string Адрес торговой точки */
	public $address = '';

	/** @var string Код торговой точки */
	public $code = '';

	/** @var string Идентификатор торговой точки */
	public $id = '';

	/** @var string Название торговой точки */
	public $name = '';

	/** @var string Признак наличия проблемы */
	public $problemIndicator = '';

	/**
	 * @inheritDoc
	 */
	public function getRuleList(): array
	{
		return [
			'id' => ['required', ['lengthMax', 36]],
			'name' => ['required', ['lengthMax', 255]],
			'code' => [['lengthMax', 10]],
			'problemIndicator' => [['in', [
				Status::OK,
				Status::PROBLEM,
				Status::WARNING,
			]]]
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string
	{
		return $this->id;
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
}