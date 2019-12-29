<?php

namespace alekciy\ofd\providers\taxcom\Request;

use alekciy\ofd\Request;

final class Login extends Request
{
	public $method = 'POST';
	public $path = '/API/v2/Login';

	/** @var string  Логин */
	public $login = '';

	/** @var string Пароль */
	public $password = '';

	/** @var string Номер договора (если есть несколько договоров) */
	public $agreementNumber = '';

	/**
	 * @inheritDoc
	 */
	public function getPropertyMap(): array
	{
		return [
			'login'           => ['body' => 'login'],
			'password'        => ['body' => 'password'],
			'agreementNumber' => ['body' => 'agreementNumber'],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getRuleList(): array
	{
		return [
			'login' => ['required'],
			'password' => ['required'],
			'agreementNumber' => [['lengthMin', 1], ['lengthMax', 50]],
		];
	}
}