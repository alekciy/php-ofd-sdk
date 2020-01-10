<?php

namespace alekciy\ffd\documents;

use alekciy\ffd\DocumentInterface;

/**
 * Фискальный документ: кассовый чек коррекции (Чек коррекции).
 */
class CheckCorrect implements DocumentInterface
{
	/** @var int Тип документа */
	public $type = self::TYPE_CHECK_CORRECT;

	/**
	 * @inheritDoc
	 */
	public function __construct(string $version, string $format, array &$init)
	{
	}
}