<?php

namespace alekciy\ffd\documents;

use alekciy\ffd\DocumentInterface;

/**
 * Фискальный документ: отчет об открытии смены (Отчет об откр. см.).
 */
class Open implements DocumentInterface
{
	/** @var int Тип документа */
	public $type = self::TYPE_OPEN;

	/**
	 * @inheritDoc
	 */
	public function __construct(string $version, string $format, array &$init)
	{
	}
}