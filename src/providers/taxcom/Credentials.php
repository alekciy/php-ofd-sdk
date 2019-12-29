<?php

namespace alekciy\ofd\providers\taxcom;

/**
 * Необходимые реквизиты доступа к сервису.
 * Необходима зарегистрироваться в сервисе после чего запросить в технической поддержке идентификатор интегратора.
 *
 * @link https://taxcom.ru/tekhpodderzhka/kontakty/ Техническая поддержка сервиса.
 * @see https://lk-ofd.taxcom.ru/ApiHelp/ Руководство пользователя API сервиса «Такском-Касса»
 */
final class Credentials extends \alekciy\ofd\Credentials
{
	/** @var string Логин */
	public $login = '';

	/** @var string Пароль */
	public $password = '';

	/** @var string Идентификатор интегратора (токен доступа). */
	public $integratorId = '';

	/**
	 * @inheritDoc
	 */
	protected function getRuleList(): array
	{
		return [
			'login'        => ['required'],
			'password'     => ['required'],
			'integratorId' => ['required'],
			'domain'       => [
				'required',
				['in', ['api-lk-ofd.taxcom.ru', 'api-tlk-ofd.taxcom.ru']]
			],
		];
	}

	/**
	 * Для получения ID интегратора $integratorId, требующегося для доступа к методам API, необходимо обратиться
	 * с соответствующим запросом в техническую поддержку (https://taxcom.ru/tekhpodderzhka/kontakty/) компании "Такском".
	 *
	 * @param string $domain Имя домена на котором находится API (api-lk-ofd.taxcom.ru промышленный, api-tlk-ofd.taxcom.ru тестовый).
	 * @param string $login Логин.
	 * @param string $password Пароль.
	 * @param string $integratorId Идентификатор интегратора.
	 * @throws \Exception
	 */
	public function __construct(string $domain, string $login, string $password, string $integratorId)
	{
		parent::__construct($domain);
		$this->login = $login;
		$this->password = $password;
		$this->integratorId = $integratorId;
		$this->validate();
	}
}