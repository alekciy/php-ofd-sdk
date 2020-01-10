<?php

namespace alekciy\ofd\providers\yandex;

use Exception;

/**
 * Необходимые реквизиты доступа к сервису.
 *
 * Необходимо получить ключи доступа отправьте заявку через форму
 * обратной связи (https://yandex.ru/dev/ofd/doc/dg/concepts/troubleshooting-docpage/#troubleshooting) или напишите
 * в службу поддержки: askofd@support.yandex.ru
 *
 * @see https://yandex.ru/dev/ofd/doc/dg/concepts/about-docpage/ Руководство пользователя API сервиса «Яндекс.ОФД»
 */
final class Credentials extends \alekciy\ofd\Credentials
{
	/** @var string аутентификационный ключ */
	public $authenticationKey = '';

	/** @var string авторизационный ключ */
	public $authorizationKey = '';

	/**
	 * @inheritDoc
	 */
	protected function getRuleList(): array
	{
		return [
			'authenticationKey' => ['required'],
			'authorizationKey'  => ['required'],
			'domain'            => ['required', ['in', ['api.ofd.yandex.net']]],
		];
	}

	/**
	 * @param string $domain Имя домена на котором находится API (api.ofd.yandex.net промышленный).
	 * @param string $authenticationKey Аутентификационный ключ.
	 * @param string $authorizationKey Авторизационный ключ.
	 * @throws Exception
	 */
	public function __construct(string $domain, string $authenticationKey, string $authorizationKey)
	{
		parent::__construct($domain);
		$this->authenticationKey = $authenticationKey;
		$this->authorizationKey = $authorizationKey;
		$this->validate();
	}
}