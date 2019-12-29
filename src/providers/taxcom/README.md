## Требования
После [регистрации](https://lk-ofd.taxcom.ru/#account/registration) в сервисе [Такском-Касса](https://lk-ofd.taxcom.ru/) и перед началом работы необходимо получить
ID интегратора. Для этого необходимо обратиться с соответствующим запросом в
[техническую поддержку компании](https://taxcom.ru/tekhpodderzhka/kontakty/) "Такском".

## Особенности сервиса
Работа с часовыми поясами. Время во всех API вызовах находится во временной зоне UTC. ККТ не передают TZ и сервис
приводит время к UTC исходя из настроект заданных в личном кабинете. Поэтому нужно следить за корректностью указания
TZ как в самой ККТ, так и в настройках сервиса для этой ККТ.

## Пример
```php
<?php

use alekciy\ofd\providers\taxcom\Client;
use alekciy\ofd\providers\taxcom\Credentials;
use alekciy\ofd\providers\taxcom\Model\Document;
use alekciy\ofd\providers\taxcom\Taxcom;

$credentials = new Credentials(
	'api-lk-ofd.taxcom.ru', // api-lk-ofd.taxcom.ru - боевой, api-tlk-ofd.taxcom.ru - тестовый
	'логин',
	'пароль',
	'ID итегратора'
);
$agreementNumber = 'номер договора';

// Инициализируем такском API клиент
$client = new Client($credentials, $agreementNumber);
$taxcom = new Taxcom($client);

// Список офисов
$outletList = $taxcom->getOutletList();
foreach ($outletList as $outlet) {
	echo 'Офис: ' . $outlet->getName() . PHP_EOL;
	// Список касс
	$cashDeskList = $taxcom->getCashDeskList($outlet);
	foreach ($cashDeskList as $cashDesk) {
		echo "\tкасса #{$cashDesk->getFnFactoryNumber()} (рег. номер {$cashDesk->getKktRegNumber()})" . PHP_EOL;
		// Список смен на кассе
		$shiftList = $taxcom->getShiftList($cashDesk);
		foreach ($shiftList as $shift) {
			echo "\t\tсмена №" . $shift->getShiftNumber() . PHP_EOL;
			// Список документов (чеки) смены
			$docList = $taxcom->getDocumentList($shift);
			foreach ($docList as $doc) {
				// Пропускаем если это не чек
				if ($doc->documentType != Document::TYPE_CHECK) {
					continue;
				}
				$sum = round($doc->sum / 100, 2);
				echo "\t\t\tФД №{$doc->fdNumber} {$sum} рублей" . PHP_EOL;
			}
		}
	}
}
```