<?php

namespace alekciy\ffd\tags;

use alekciy\ffd\BaseTag;
use Exception;

/**
 * Номер фискального документа (ФД).
 *
 * Порядковый номер ФД с момента формирования отчета о регистрации ККТ или отчета об изменении параметров регистрации
 * ККТ в связи с заменой фискального накопителя.
 */
class Tag1040 extends BaseTag
{
	/** @var string */
	public $value = '';

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	protected function init(array &$init)
	{
		$code = self::getCode();
		if (isset($init[$code])) {
			$this->value = (string) $init[$code];
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function getRuleList(): array
	{
		return [
			'value' => ['required', 'integer', ['min', 1], ['max', 4294967295]],
		];
	}
}