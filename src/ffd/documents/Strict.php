<?php

namespace alekciy\ffd\documents;

use alekciy\ffd\DocumentInterface;

/**
 * Фискальный документ: бланк строгой отчетности (БСО).
 */
class Strict implements DocumentInterface
{
	/** @var int Тип документа */
	public $type = self::TYPE_STRICT;

	/**
	 * @inheritDoc
	 */
	public function __construct(string $version, string $format, array &$init)
	{
	}
}