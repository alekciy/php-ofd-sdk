<?php

namespace alekciy\ffd\documents;

use alekciy\ffd\DocumentInterface;

/**
 * Фискальный документ: отчет об изменении параметров регистрации (Отчет о перерег.).
 */
class RegistrationChange implements DocumentInterface
{
	/** @var int Тип документа */
	public $type = self::TYPE_REGISTRATION_CHANGE;

	/**
	 * @inheritDoc
	 */
	public function __construct(string $version, string $format, array &$init)
	{
	}
}