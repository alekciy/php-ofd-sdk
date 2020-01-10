<?php

namespace alekciy\ffd\documents;

use alekciy\ffd\DocumentInterface;

/**
 * Фискальный документ: отчет о регистрации (Отчет о рег.).
 */
class Registration implements DocumentInterface
{
	/** @var int Тип документа */
	public $type = self::TYPE_REGISTRATION;

	/**
	 * @inheritDoc
	 */
	public function __construct(string $version, string $format, array &$init)
	{
	}
}