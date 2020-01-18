<?php

namespace alekciy\ofd;

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
		$propertyMap = $this->getPropertyInitMap();
		foreach ($init as $propertyName => $propertyValue) {
			// Если задано преобразование имени свойства (или трансформация)
			if (isset($propertyMap[$propertyName])) {
				$propertyMapValue = $propertyMap[$propertyName];
				if (is_array($propertyMapValue)) {
					// Задаем имя свойства модели
					if (isset($propertyMapValue[0])) {
						$propertyName = $propertyMapValue[0];
					}
					// Преобразуем значение вызвав заданный метод модели
					if (isset($propertyMapValue['conv'])) {
						if (!method_exists($this, $propertyMapValue['conv'])) {
							throw new Exception(sprintf('В объекте %s отсутствует метод %s(), задайте корректное имя в getPropertyInitMap(): [%s => [conv => МЕТОД]]',
								get_class($this),
								$propertyMapValue['conv'],
								$propertyName
							));
						}
						$propertyValue = $this->{$propertyMapValue['conv']}($propertyValue);
					}
				} else {
					$propertyName = $propertyMap[$propertyName];
				}
			}

			if (property_exists($this, $propertyName)) {
				$this->$propertyName = $propertyValue;
			} else {
				$propertyName = self::camelize($propertyName);
				if (property_exists($this, $propertyName)) {
					$this->$propertyName = $propertyValue;
				}
			}
		}
		// Проверяем корректность свойств модели
		$this->validate();
	}

	/**
	 * В потомках метод должен возращать карту преобразований имен при инициализации (ключ) и свойств объекта (значение).
	 * Если значение это массив, то при наличие ключа conv к инициализационному значению будет применена функция заданная
	 * значением этого ключа. Например: ['totalSum' => ['sum', 'conv'=> 'RubToKop']], значение ключа totalSum из массива
	 * инициализации будет через вызов метода RubToKop($value), конвертация рублей в копейки, сохранено в свойство sum
	 * модели.
	 *
	 * @return array
	 */
	abstract protected function getPropertyInitMap(): array;

	/**
	 * Конвертировать строку $str (представленную в utf8 кодировке) в camelCase.
	 *
	 * @param $str
	 * @return string
	 */
	static public function camelize($str): string
	{
		$str = mb_convert_case($str, MB_CASE_TITLE, 'utf8');
		$str = preg_replace('~[\-_\s]~ui', '', $str);
		$firstLetter = mb_substr($str, 0, 1, 'utf8');
		return mb_convert_case($firstLetter, MB_CASE_LOWER, 'utf8') . mb_substr($str, 1, mb_strlen($str)-1, 'utf8');
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
	 * Выполнить проверку корректности свойств модели.
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