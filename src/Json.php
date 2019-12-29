<?php

namespace alekciy\ofd;

use InvalidArgumentException;

/**
 * Класс для единообразного кодирования/декодирования JSON в приложении.
 */
class Json
{
	/**
	 * Сериализовать переменную $value в строку формата JSON.
	 *
	 * @link http://php.net/manual/ru/function.json-encode.php
	 * @link http://php.net/manual/ru/json.constants.php
	 *
	 * @param mixed $value     Значение, которое будет закодировано. Может быть любого типа за исключением resource.
	 * @param integer $options Битовая маска, составляемая из значений JSON констант.
	 * @param integer $depth   Максимальная глубина.
	 * @throws InvalidArgumentException
	 * @return string
	 */
	public static function encode($value, $options = 0, $depth = 512)
	{
		$options = $options | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK;
		$result = json_encode($value, $options, $depth);
		if (JSON_ERROR_NONE != json_last_error()) {
			throw new InvalidArgumentException(json_last_error_msg(), json_last_error());
		}

		return $result;
	}

	/**
	 * Десериализовать строку $json в формате JSON в переменную.
	 *
	 * @link http://php.net/manual/ru/function.json-decode.php
	 * @link http://php.net/manual/ru/json.constants.php
	 *
	 * @param string $json     Строка с формате JSON. Эта функция работает только со строками в UTF-8 кодировке.
	 * @param bool $assoc      Если TRUE, возвращаемые объекты будут преобразованы в ассоциативные массивы.
	 * @param integer $depth   Максимальная глубина рекурсии.
	 * @param integer $options Битовая маска, составляемая из значений JSON констант.
	 * @throws InvalidArgumentException
	 * @return mixed
	 */
	public static function decode($json, $assoc = true, $depth = 512, $options = 0)
	{
		$result = json_decode($json, $assoc, $depth, $options);
		if (JSON_ERROR_NONE != json_last_error()) {
			throw new InvalidArgumentException(json_last_error_msg(), json_last_error());
		}

		return $result;
	}
}
