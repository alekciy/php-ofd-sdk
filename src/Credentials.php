<?php

namespace alekciy\ofd;

use Exception;
use InvalidArgumentException;
use Valitron\Validator;

/**
 * Базовый класс для задания реквизитов доступа у конкретных провайдеров.
 */
abstract class Credentials
{
	/** @var string Имя домена на котором находится API */
	public $domain = '';

	/**
	 * Имя домена на котором находится API.
	 *
	 * @param string $domain
	 */
	public function __construct(string $domain)
	{
		$this->domain = $domain;
	}

	/**
	 * Правила валидации в виде массива:
	 *   ключ - имя проверяемого свойства объекта;
	 *   значение - массив с правилами валидации.
	 * Например, свойство domain является обязательным для заполнения: ['domain', ['required']]
	 *
	 * @see https://github.com/vlucas/valitron#built-in-validation-rules
	 * @return array
	 */
	abstract protected function getRuleList(): array;

	/**
	 * Выполнить проверку корректности реквизитов доступа.
	 *
	 * @param string $lang Язык на котором выводятся сообщения с ошибками валидации.
	 * @return void
	 * @throws Exception
	 * @throws InvalidArgumentException
	 */
	public function validate($lang = 'ru')
	{
		Validator::lang($lang);
		$validator = new Validator(get_object_vars($this));
		// Задаем правила валидации
		$validator->mapFieldRules('domain', ['required']);
		// Правила валидации дочерних классов
		$validator->mapFieldsRules($this->getRuleList());
		if (!$validator->validate()) {
			$messageList = [];
			foreach ($validator->errors() as  $propertyName => $errorList) {
				$messageList[] = $propertyName . ': ' . implode(', ', $errorList);
			}
			throw new Exception(implode('. ', $messageList));
		}
	}
}
