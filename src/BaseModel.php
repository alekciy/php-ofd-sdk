<?php

namespace alekciy\ofd;

use alekciy\ofd\providers\taxcom\Model\CashDeskShort;
use Exception;
use InvalidArgumentException;
use Valitron\Validator;

/**
 * Базовый класс для моделей ответов от API провайдера.
 * Объект класса при создании проходит проверку, поэтому если создание прошло без генерации исключения, значит объект валиден.
 */
abstract class BaseModel
{
	/**
	 * @param array $init Массив значений свойств инициализации.
	 * @throws Exception
	 */
	public function __construct(array $init)
	{
		foreach ($init as $propertyName => $propertyValue) {
			if (property_exists($this, $propertyName)) {
				$this->$propertyName = $propertyValue;
			}
		}
		$this->validate();
	}

	/**
	 * Правила валидации в виде массива: ключ - имя правила, значение - массив с именами свойств для которых правило будет
	 * проверяться. Например, свойство method является обязательным для заполнения: ['method', ['required']]
	 *
	 * @see https://github.com/vlucas/valitron#built-in-validation-rules
	 * @return array
	 */
	abstract protected function getRuleList(): array;

	/**
	 * Выполнить проверку корректности запроса.
	 *
	 * @param string $lang Язык на котором выводятся сообщения с ошибками валидации.
	 * @return void
	 * @throws Exception
	 * @throws InvalidArgumentException
	 */
	protected function validate($lang = 'ru')
	{
		Validator::lang($lang);
		$validator = new Validator((array) $this);
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