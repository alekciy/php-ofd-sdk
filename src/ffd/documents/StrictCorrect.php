<?php

namespace alekciy\ffd\documents;

use alekciy\ffd\DocumentInterface;

/**
 * Фискальный документ: бланк строгой отчетности коррекции (БСО коррекции).
 */
class StrictCorrect implements DocumentInterface
{
	/** @var int Тип документа */
	public $type = self::TYPE_STRICT_CORRECT;

	/**
	 * @inheritDoc
	 */
	public function __construct(string $version, string $format, array &$init)
	{
	}
}