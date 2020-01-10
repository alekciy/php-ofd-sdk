<?php

namespace alekciy\ffd\documents;

use alekciy\ffd\DocumentInterface;

/**
 * Фискальный документ: отчет о текущем состоянии расчетов (Отчет о расч.).
 */
class State implements DocumentInterface
{
	/** @var int Тип документа */
	public $type = self::TYPE_STATE;

	/**
	 * @inheritDoc
	 */
	public function __construct(string $version, string $format, array &$init)
	{
	}
}