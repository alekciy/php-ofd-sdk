<?php

namespace alekciy\ffd\documents;

use alekciy\ffd\DocumentInterface;

/**
 * Фискальный документ: отчет о закрытии фискального накопителя (Отч. о закр. ФН).
 */
class FnClose implements DocumentInterface
{
	/** @var int Тип документа */
	public $type = self::TYPE_FN_CLOSE;

	/**
	 * @inheritDoc
	 */
	public function __construct(string $version, string $format, array &$init)
	{
	}
}