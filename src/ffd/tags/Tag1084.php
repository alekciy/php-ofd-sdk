<?php

namespace alekciy\ffd\tags;

use alekciy\ffd\BaseTag;
use Exception;

/**
 * Дополнительный реквизит пользователя.
 *
 * Дополнительный реквизит пользователя с учетом особенностей сферы деятельности, в которой осуществляются расчеты.
 */
class Tag1084 extends BaseTag
{
	/** @var array */
	public $value = '';

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	protected function init(array &$init)
	{
		$code = self::getCode();
		if (isset($init[$code])
			&& is_array($init[$code])
		) {
			if (count($init[$code]) != 1) {
				throw new Exception('Реквизит может содержать только одно значение');
			}
			$this->value = [];
			foreach ($init[$code] as $extItem) {
				if (isset($extItem[Tag1085::getCode()])
					&& isset($extItem[Tag1086::getCode()])
				) {
					$tagExtName = new Tag1085($this->documentType, $this->documentForm, $this->documentVersion,$extItem);
					$tagExtValue = new Tag1086($this->documentType, $this->documentForm, $this->documentVersion,$extItem);
					$this->value[$tagExtName->value] = $tagExtValue->value;
				}
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function getRuleList(): array
	{
		return [
			'value' => ['array'],
		];
	}
}