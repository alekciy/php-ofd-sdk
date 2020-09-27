<?php

namespace alekciy\ffd\documents;

use alekciy\ffd\DocumentInterface;
use alekciy\ffd\tags\Tag1012;
use alekciy\ffd\tags\Tag1020;
use alekciy\ffd\tags\Tag1037;
use alekciy\ffd\tags\Tag1040;
use alekciy\ffd\tags\Tag1041;
use alekciy\ffd\tags\Tag1084;
use Exception;

/**
 * Фискальный документ: кассовый чек (Кассовый чек).
 */
class Check implements DocumentInterface
{
	/** @var int Тип документа */
	public $type = self::TYPE_CHECK;

	/** @var Tag1041 Заводской номер ФН */
	public $fnFactoryNumber;

	/** @var Tag1040 Номер фискального документа (ФД) */
	public $fdNumber;

	/** @var Tag1037 Регистрационный номер контрольно-кассовой техники (ККТ) */
	public $kktRegNumber;

	/** @var Tag1012 Дата и время формирования фискального документа (во временной зоне ККТ) */
	public $createAt;

	/** @var Tag1020 ИТОГ, включая размер НДС и наценки */
	public $total;

	/** @var Tag1084 Дополнительный реквизит пользователя */
	public $extend;

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public function __construct(string $version, string $format, array &$init)
	{
		$this->createAt        = new Tag1012($this->type, $format, $version, $init);
		$this->total           = new Tag1020($this->type, $format, $version, $init);
		$this->kktRegNumber    = new Tag1037($this->type, $format, $version, $init);
		$this->fdNumber        = new Tag1040($this->type, $format, $version, $init);
		$this->fnFactoryNumber = new Tag1041($this->type, $format, $version, $init);
		$this->extend          = new Tag1084($this->type, $format, $version, $init);
	}
}
