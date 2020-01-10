<?php

namespace alekciy\ffd;

use Exception;
use InvalidArgumentException;
use Valitron\Validator;

abstract class BaseTag
{
	/** @var integer Тип документа, DocumentInterface::TYPE_* */
	public $documentType;

	/** @var string Формат документа, DocumentInterface::FORMAT_* */
	public $documentForm;

	/** @var string Версия ФФД документа, DocumentInterface::VERSION_* */
	public $documentVersion;

	/** @var mixed Значение тега */
	public $value;

	/**
	 * Текстовой идентификатор тега.
	 *
	 * @return string
	 * @throws Exception
	 */
	public static function getCode(): string
	{
		if (preg_match('~Tag(?<code>[0-9]{4})~u', static::class, $matches) === 1) {
			return $matches['code'];
		}
		throw new Exception('Название класса потомка должно соответствовать шаблону Tag[0-9]{4}');
	}

	/**
	 * Параметры инициализации, массив:
	 *   ключ - идентификатор тега;
	 *   значение - значение тега.
	 *
	 * @param array $init
	 * @return void
	 */
	abstract protected function init(array &$init);

	/**
	 * Правила валидации в виде массива в котором должен быть минимум один ключ 'value' для проверки свойства
	 * $this->value - значение тега и он должен быть обязательным.
	 * В потомках можно учитывать контекст типа документа ($this->documentType), формата ($this->documentForm) и версии
	 * ФФД ($this->documentVersion) для возврата разных правил валидации.
	 *
	 * @see https://github.com/vlucas/valitron#built-in-validation-rules
	 * @return array
	 */
	abstract protected function getRuleList(): array;

	/**
	 * @param int $documentType Тип документа.
	 * @param string $documentForm Формат документа.
	 * @param string $documentVersion Версия ФФД.
	 * @param array $init Параметры инициализации.
	 * @throws Exception
	 */
	public function __construct(
		int $documentType,
		string $documentForm,
		string $documentVersion,
		array &$init
	) {
		$this->documentType = $documentType;
		$this->documentForm = $documentForm;
		$this->documentVersion = $documentVersion;
		$this->validate($this->getBaseRuleList());

		$this->init($init);

		$tagRuleList = $this->getRuleList();
		if (!isset($tagRuleList['value'])) {
			throw new Exception('Метод ' . get_class($this) . '::getRuleList() должен возращать ключ value');
		}
		$this->validate($this->getRuleList());
	}

	/**
	 * Выполнить проверку корректности запроса.
	 *
	 * @see https://github.com/vlucas/valitron#built-in-validation-rules
	 * @param array $ruleList Правила валидации.
	 * @param string $lang Язык на котором выводятся сообщения с ошибками валидации.
	 * @return void
	 * @throws Exception
	 */
	protected function validate(array $ruleList, $lang = 'ru')
	{
		$methodName = get_class($this) == __CLASS__
			? 'getBaseRuleList()'
			: 'getRuleList()';

		Validator::lang($lang);
		$validator = new Validator((array) $this);
		try {
			$validator->mapFieldsRules($ruleList);
		} catch (InvalidArgumentException $e) {
			throw new Exception(get_class($this) . " ошибка в методе {$methodName}: " . $e->getMessage());
		}
		if (!$validator->validate()) {
			$messageList = [];
			foreach ($validator->errors() as  $propertyName => $errorList) {
				$messageList[] = get_class($this) . "::{$methodName}[{$propertyName}] " . implode(', ', $errorList);
			}
			throw new Exception(implode('. ', $messageList));
		}
	}

	/**
	 * Правила валидации в виде массива:
	 *   ключ - имя правила,
	 *   значение - массив с именами свойств для которых правило будет проверяться.
	 *
	 * @see https://github.com/vlucas/valitron#built-in-validation-rules
	 * @return array
	 */
	private function getBaseRuleList()
	{
		return [
			'documentType' => ['required', ['in', [
				DocumentInterface::TYPE_REGISTRATION,
				DocumentInterface::TYPE_OPEN,
				DocumentInterface::TYPE_CHECK,
				DocumentInterface::TYPE_STRICT,
				DocumentInterface::TYPE_CLOSE,
				DocumentInterface::TYPE_FN_CLOSE,
				DocumentInterface::TYPE_CONFIRMATION,
				DocumentInterface::TYPE_REGISTRATION_CHANGE,
				DocumentInterface::TYPE_STATE,
				DocumentInterface::TYPE_CHECK_CORRECT,
				DocumentInterface::TYPE_STRICT_CORRECT,
			]]],
			'documentForm' => ['required', ['in', [
				DocumentInterface::FORMAT_PRINT,
				DocumentInterface::FORMAT_ELECTRONIC,
			]]],
			'documentVersion' => ['required', ['in', [
				DocumentInterface::VERSION_1_05,
				DocumentInterface::VERSION_1_10,
			]]],
		];
	}
}