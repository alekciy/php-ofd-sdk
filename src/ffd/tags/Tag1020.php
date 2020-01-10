<?php

namespace alekciy\ffd\tags;

use alekciy\ffd\BaseTag;
use alekciy\ffd\DocumentInterface;
use Exception;

/**
 * Сумма расчета, указанного в чеке.
 *
 * Сумма расчета с учетом скидок, наценок и НДС.
 */
class Tag1020 extends BaseTag
{
	/** @var integer|float */
	public $value = '';

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	protected function init(array &$init)
	{
		$code = self::getCode();
		if (isset($init[$code])) {
			if ($this->documentForm == DocumentInterface::FORMAT_PRINT
				&& (is_float($init[$code]) || is_integer($init[$code]))
			) {
				$this->value = round($init[$code], 2);
			} else {
				$this->value = $init[$code];

			}
		}
	}

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	protected function getRuleList(): array
	{
		if ($this->documentForm == DocumentInterface::FORMAT_PRINT) {
			return [
				'value' => ['required', 'numeric', ['min', 0], ['max', 999999]],
			];
		} elseif ($this->documentForm == DocumentInterface::FORMAT_ELECTRONIC) {
			return [
				'value' => ['required', 'integer'],
			];
		}
		throw new Exception('Неизвестный формат документа');
	}
}