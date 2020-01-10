<?php

namespace alekciy\ffd\documents;

use alekciy\ffd\DocumentInterface;

/**
 * Фискальный документ: подтверждение оператора (Подтверждение).
 */
class Confirmation implements DocumentInterface
{
	/** @var int Тип документа */
	public $type = self::TYPE_CONFIRMATION;

	/**
	 * @inheritDoc
	 */
	public function __construct(string $version, string $format, array &$init)
	{
	}
}