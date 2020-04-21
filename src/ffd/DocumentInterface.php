<?php

namespace alekciy\ffd;

/**
 * Формат фискальных документов (ФФД).
 *
 * @see http://pravo.gov.ru/proxy/ips/?docbody=&nd=102431615 Приказ ФНС России от 21.03.2017 №ММВ-7-20/229@
 * (ред. от 22.10.2018) "Об утверждении дополнительных реквизитов фискальных документов и форматов фискальных документов,
 * обязательных к использованию"
 */
interface DocumentInterface
{
	// Типы фискальных документов
	const TYPE_REGISTRATION        = 1;  // отчет о регистрации (Отчет о рег.)
	const TYPE_OPEN                = 2;  // отчет об открытии смены (Отчет об откр. см.)
	const TYPE_CHECK               = 3;  // кассовый чек (Кассовый чек)
	const TYPE_STRICT              = 4;  // бланк строгой отчетности (БСО)
	const TYPE_CLOSE               = 5;  // отчет о закрытии смены (Отч. о закр. см.)
	const TYPE_FN_CLOSE            = 6;  // отчет о закрытии фискального накопителя (Отч. о закр. ФН)
	const TYPE_CONFIRMATION        = 7;  // подтверждение оператора (Подтверждение)
	const TYPE_REGISTRATION_CHANGE = 11; // отчет об изменении параметров регистрации (Отчет о перерег.)
	const TYPE_STATE               = 21; // отчет о текущем состоянии расчетов (Отчет о расч.)
	const TYPE_CHECK_CORRECT       = 31; // кассовый чек коррекции (Чек коррекции)
	const TYPE_STRICT_CORRECT      = 41; // бланк строгой отчетности коррекции (БСО коррекции)

	// Форматы фискальных документов
	const FORMAT_PRINT      = 'print';      // печатная форма (ПФ)
	const FORMAT_ELECTRONIC = 'electronic'; // электронная форма (ЭФ)

	// Версии фискальных документов
	const VERSION_1_05 = '1.05';
	const VERSION_1_10 = '1.10';

	/**
	 * Параметры инициализации должны быть массивом:
	 *   ключ - идентификатор тега;
	 *   значение - значение тега, либо скаляр, либо такой же массив вложенных тегов.
	 *
	 * @param string $version Тип документа (self::VERSION_*)
	 * @param string $format  Формат документа (self::FORMAT_*)
	 * @param array $init     Параметры инициализации.
	 */
	public function __construct(string $version, string $format, array &$init);
}
