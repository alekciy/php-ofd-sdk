<?php

use alekciy\ofd\providers\taxcom\Credentials;

return [
	'credentials' => new Credentials(
		'api-lk-ofd.taxcom.ru',
		'логин',
		'пароль',
		'ID интегратора'
	),
	'agreementNumber' => 'номер договора',
];
