## Описание
Библиотека предоставляет унифицированный интерфейс (см. [ProviderInterface](https://github.com/alekciy/php-ofd-sdk/blob/master/src/interfaces/ProviderInterface.php))
работы с различными ОФД (оператор фискальных данных) при получении данных о фискальных документах (чеках).

Основная цель - облегчить интеграцию проекта при работе с разными API операторов. Например, при синхронизации данных
о выбитых на кассе чеках и данных ушедших в налоговую.

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alekciy/php-ofd-sdk/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alekciy/php-ofd-sdk/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/alekciy/php-ofd-sdk/badges/build.png?b=master)](https://scrutinizer-ci.com/g/alekciy/php-ofd-sdk/build-status/master)

## Поддерживаемые ОФД
 * [ООО «Такском»](https://taxcom.ru/ofd/)
 * [ООО «Яндекс.ОФД»](https://ofd.yandex.ru/)

## Установка
Через composer командой: `composer require alekciy/php-ofd-sdk`

## Пример
Ниже приведен пример работы для Такском и Яндекс.ОФД.
```php
<?php

include_once __DIR__ . '/vendor/autoload.php';

use alekciy\ofd\providers\taxcom\Client as TaxcomClient;
use alekciy\ofd\providers\taxcom\Credentials as TaxcomCredentials;
use alekciy\ofd\providers\taxcom\Taxcom;
use alekciy\ofd\providers\yandex\Client as YandexClient;
use alekciy\ofd\providers\yandex\Credentials as YandexCredentials;
use alekciy\ofd\providers\yandex\Yandex;

// ============ Инициализация клиента ============
// У каждого провайдера свои требование при работе через API поэтому инициализация клиента
// зависит от используемого провайдера.

// Инициализируем Такском API клиент
$credentials = new TaxcomCredentials(
	'api-lk-ofd.taxcom.ru',
	'логин',
	'пароль',
	'токен'
);
$agreementNumber = 'Номер договора';
$client = new TaxcomClient($credentials, $agreementNumber);
$taxcom = new Taxcom($client);

// Инициализация Яндекс.ОФД клиент
$credentials = new YandexCredentials(
	'api.ofd.yandex.net',
	'аутентификационный ключ',
	'авторизационный ключ'
);
$client = new YandexClient($credentials);
$yandex = new Yandex($client);

// ============ Получение данных ============
// Получаем список точек продаж с Такском...
$outletList = $taxcom->getOutletList();
$outlet = current($outletList);
// ...список касс с первой точки...
$cashDeskList = $taxcom->getCashDeskList($outlet);
$cashDesk = current($cashDeskList);
// ...и список смен с первой кассы
$shiftList = $taxcom->getShiftList($cashDesk);

// Получение смен через Яндекс.ОФД выглядит точно так же
$outletList = $yandex->getOutletList();
$outlet = current($outletList);
$cashDeskList = $taxcom->getCashDeskList($outlet);
$cashDesk = current($cashDeskList);
$shiftList = $taxcom->getShiftList($cashDesk);
```

## Тесты
В библиотеке используются два вида тестов:
 * юнит для проверки правильности работы самой библиотеки;
 * интеграционные для проверки работы API заданного ОФД.

### Юнит тесты
Для запуска тестов нужно:
 1. выполнить команду`composer test-unit`.

### Интеграционные тесты
Для запуска тестов нужно:
 1. скопировать файл нужного _ОФД_ `tests/credentials/ОФД.example.php` в файл `tests/credentials/ОФД.php`;
 1. вписать требуемые реквизиты доступа в `tests/credentials/ОФД.php`;
 1. выполнить команду `composer test-integration-ОФД`.


## Основные термины
В таблице приведены термины в порядке удобном для понимания.
<table>
    <tr>
        <td><strong>ОФД</strong></td>
        <td><strong>О</strong>ператор <strong>Ф</strong>искальных <strong>Д</strong>анных</td>
        <td>Сервис принимающий с кассово аппарата данные о выбитых чеках и передающий их в налоговую службу.</td>
    </tr>
    <tr>
        <td><strong>ККТ</strong></td>
        <td><strong>К</strong>онтрольно <strong>К</strong>ассовая <strong>Т</strong>ехника</td>
        <td>Кассовый аппарат выбивающий чеки либо на бумаге, либо в электронном виде.</td>
    </tr>
    <tr>
        <td><strong>ККМ</strong></td>
        <td><strong>К</strong>онтрольно <strong>К</strong>ассовая <strong>М</strong>ашина</td>
        <td>Устаревшее название ККТ.</td>
    </tr>
    <tr>
        <td><strong>ФД</strong></td>
        <td><strong>Ф</strong>искальный <strong>Д</strong>окумент</td>
        <td>Документ отправляемый в налоговую службу. Кассовый чек является частным случаем ФД. Все типы ФД
        перечисленые в константах <a href="https://github.com/alekciy/php-ofd-sdk/blob/master/src/ffd/DocumentInterface.php#L14">DocumentInterface</a>
        в виде классов <a href="https://github.com/alekciy/php-ofd-sdk/tree/master/src/ffd/documents">документов</a>.
        </td>
    </tr>
    <tr>
        <td><strong>ФФД</strong></td>
        <td><strong>Ф</strong>ормат <strong>Ф</strong>искальных <strong>Д</strong>анных</td>
        <td>По сути спецификация описывающая свойства (реквизиты) и их значения, которые могут быть у ФД.
        Регламентируется приказами ФНС России (на основании 54-ФЗ). Например, <a href="https://www.nalog.gov.ru/rn77/about_fts/docs/10020801/">приказ № ЕД-7-20/662</a>
        По состоянию на 2022 год есть три версии ФФД: 1.0, 1.05, 1.1 и 1.2.
        </td>
    </tr>
    <tr>
        <td><strong>Тег ФД</strong></td>
        <td>-</td>
        <td>По сути имя свойства (реквизита) ФД которые передаются в ОФД. Например, в теге 1037 касса передает
        свой регистрационный номер. Поддерживаемые теги находятся в директории <a href="https://github.com/alekciy/php-ofd-sdk/tree/master/src/ffd/tags">tags</a>.
        </td>
    </tr>
</table>
