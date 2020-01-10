<?php

namespace alekciy\ffd\tags;

use alekciy\ffd\BaseTag;
use Exception;

/**
 * Регистрационный номер фискального накопителя (ФН).
 */
class Tag1041 extends BaseTag
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
			'value' => ['required', ['lengthMin', 1], ['lengthMax', 16]],
		];
	}
}