<?php

namespace alekciy\ofd\providers\taxcom\Model;

use alekciy\ofd\BaseModel;
use alekciy\ofd\interfaces\CashDeskInterface;
use alekciy\ofd\providers\taxcom\Status;

/**
 * Детальная информация по кассе (ККТ).
 */
class CashDesk extends BaseModel implements CashDeskInterface
{
	/** @var string Оплачена по */
	public $cashdeskEndDateTime;

	/** @var string Состояние  */
	public $cashdeskState;

	/** @var string Срок действия ФН */
	public $fnDuration;

	/** @var string Дата окончания действия ФН */
	public $fnEndDateTime;

	/** @var string Заводской номер ФН */
	public $fnFactoryNumber;

	/** @var string Дата регистрации ФН */
	public $fnRegDateTime;

	/** @var string Состояние ФН */
	public $fnState;

	/** @var string Заводской номер */
	public $kktFactoryNumber;

	/** @var string Модель */
	public $kktModelName;

	/** @var string Регистрационный номер */
	public $kktRegNumber;

	/** @var string Дата последнего документа */
	public $lastDocumentDateTime;

	/** @var string Статус последнего документа */
	public $lastDocumentState;

	/** @var string Название */
	public $name;

	/** @var string Статус смены */
	public $shiftStatus;

	// Состояние
	const STATUS_ACTIVE = 'Active'; // Подключена
	const STATUS_EXPIRES = 'Expires'; // Заканчивается оплата
	const STATUS_EXPIRED = 'Expired'; // Не оплачена
	const STATUS_INACTIVE = 'Inactive'; // Отключена пользователем
	const STATUS_ACTIVATION = 'Activation'; // Подключение
	const STATUS_DEACTIVATION = 'Deactivation'; // Отключение
	const STATUS_FN_CHANGE = 'FNChange'; // Замена ФН
	const STATUS_FN_REGISTRATION = 'FNSRegistration'; // Регистрация в ФНС
	const STATUS_FN_REGISTRATION_ERROR = 'FNSRegistrationError'; // Ошибка регистрации в ФНС

	// Состояние ФН
	const FN_STATUS_ACTIVE = 'Active'; // Активен
	const FN_STATUS_EXPIRES = 'Expires'; // Срок истекат
	const FN_STATUS_EXPIRED = 'Expired'; // Срок истек

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
			'kktModelName' => [['lengthMin', 1], ['lengthMax', 16]],
			'fnDuration' => [['lengthMin', 1], ['lengthMax', 20]],
			'shiftStatus' => [['in', [
				Shift::STATUS_OPEN,
				Shift::STATUS_CLOSE,
			]]],
			'cashdeskState' => [['in', [
				self::STATUS_ACTIVE,
				self::STATUS_EXPIRES,
				self::STATUS_EXPIRED,
				self::STATUS_INACTIVE,
				self::STATUS_ACTIVATION,
				self::STATUS_DEACTIVATION,
				self::STATUS_FN_CHANGE,
				self::STATUS_FN_REGISTRATION,
				self::STATUS_FN_REGISTRATION_ERROR,
			]]],
			'fnState' => [['in', [
				self::FN_STATUS_ACTIVE,
				self::FN_STATUS_EXPIRES,
				self::FN_STATUS_EXPIRED,
			]]],
			'lastDocumentState' => [['in', [
				Status::OK,
				Status::PROBLEM,
				Status::WARNING,
			]]],
			'fnRegDateTime' => [['dateFormat', 'H-m-dTH:i:s']],
			'cashdeskEndDateTime' => [['dateFormat', 'H-m-dTH:i:s']],
			'fnEndDateTime' => [['dateFormat', 'H-m-dTH:i:s']],
			'lastDocumentDateTime' => [['dateFormat', 'H-m-dTH:i:s']],
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
}