<?php

namespace alekciy\ofd;

use Exception;
use InvalidArgumentException;
use Valitron\Validator;

/**
 * Класс запроса к провайдеру.
 */
abstract class Request
{
	/** @var string HTTP метод */
	public $method = '';

	/** @var string Путь до документа */
	public $path = '';

	public $debug = false;

	/**
	 * @param array $init Массив значений свойств инициализации.
	 * @throws Exception
	 */
	public function __construct(array $init)
	{
		foreach ($init as $propertyName => $propertyValue) {
			if (isset($this->$propertyName)) {
				$this->$propertyName = $propertyValue;
			}
		}
		$this->validate();
	}

	/**
	 * В потомках вызывать родительский метод и делать array_merge().
	 * Карта соответствия имени свойства (ключ) объекта и имени перенной в API (значение). Все свойства которые должны
	 * передаваться при запросе в API должны быть тут перечислены. При этом значение это массив:
	 *   ключ - куда уходит переменная: query (в URL), body (тело запроса);
	 *   значение - имя переменной в API
	 * Пример: ['pageNumber' => ['query' => 'pn'].
	 *
	 * @var array
	 */
	abstract protected function getPropertyMap(): array;

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
		// Задаем правила валидации
		$validator->mapFieldRules('method', [
			'required',
			['in', ['POST', 'GET']],
		]);
		$validator->mapFieldRules('path', ['required']);
		$validator->mapFieldRules('debug', ['boolean']);
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

	/**
	 * Получить тело запроса для отправки в API.
	 *
	 * @return array
	 * @throws Exception
	 */
	public function getBody(): array
	{
		$result = [];
		foreach ($this->getPropertyMap() as $propertyName => $filter) {
			if (!isset($filter['body'])) {
				continue;
			}
			if (!empty($this->$propertyName)) {
				$result[$filter['body']] = $this->$propertyName;
			}
		}
		return $result;
	}

	/**
	 * Получить параметры запроса.
	 *
	 * @return array
	 * @throws Exception
	 */
	public function getQuery(): array
	{
		$result = [];
		foreach ($this->getPropertyMap() as $propertyName => $filter) {
			if (!isset($filter['query'])) {
				continue;
			}
			$result[$filter['query']] = $this->$propertyName;
		}
		return $result;
	}
}