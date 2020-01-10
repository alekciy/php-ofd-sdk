<?php

namespace alekciy\ffd\documents;

use alekciy\ffd\DocumentInterface;

/**
 * Фискальный документ: отчет о закрытии смены (Отч. о закр. см.).
 */
class Close implements DocumentInterface
{
	/** @var int Тип документа */
	public $type = self::TYPE_CLOSE;

	/**
	 * @inheritDoc
	 */
	public function __construct(string $version, string $format, array &$init)
	{
	}
}