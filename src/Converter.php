<?php

namespace alekciy\ofd;

use InvalidArgumentException;

trait Converter
{
	/**
	 * Преобразует сумму $sum заданную в рублях в сумму в копейках. В случае задания суммы строкой разделитель дробной
	 * части может быть точкой или запятой. Разделитель разрядов не поддерживается, т.е. передавать '2 000,35' нельзя.
	 *
	 * @param int|float|string $sum
	 * @return int
	 */
	public static function RubToKop($sum): int
	{
		if (is_integer($sum)) {
			return $sum;
		} elseif (is_float($sum)) {
			return intval($sum * 100);
		} elseif (is_string($sum)
			&& preg_match('~^\s?[0-9]+[.,]?[0-9]*\s?$~u', $sum) === 1
		) {
			return intval($sum * 100);
		}
		throw new InvalidArgumentException('Неправильный денежный формат');
	}
}