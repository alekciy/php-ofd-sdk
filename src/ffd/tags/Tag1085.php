<?php

namespace alekciy\ffd\tags;

use alekciy\ffd\BaseTag;
use Exception;

/**
 * Наименование дополнительного реквизита пользователя.
 *
 * Наименование дополнительного реквизита пользователя с учетом особенностей сферы деятельности, в которой осуществляются расчеты.
 */
class Tag1085 extends BaseTag
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
			'value' => [['lengthMin', 1], ['lengthMax', 64]],
		];
	}
}