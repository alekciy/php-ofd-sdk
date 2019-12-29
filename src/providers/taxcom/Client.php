<?php

namespace alekciy\ofd\providers\taxcom;

use alekciy\ofd\Json;
use alekciy\ofd\providers\taxcom\Request\Login;
use alekciy\ofd\Request;
use Exception;
use GuzzleHttp\Client as httpClient;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class Client
{
	/** @var httpClient HTTP клиент для низкоуровневой работы с API */
	protected $httpClient = null;

	/** @var Credentials Реквизиты доступа */
	protected $credentials = null;

	/** @var string Номер договора */
	protected $agreementNumber = '';

	/** @var string Сессионный токен */
	protected $sessionToken = '';

	/** @var string Версия клиента */
	private $version = '0.1.0';

	/**
	 * @param Credentials $credentials Реквизиты доступа.
	 * @param string $agreementNumber  Номер договора.
	 * @param string $sessionToken     Сессионный токен доступа.
	 * @throws Exception
	 */
	public function __construct(Credentials $credentials, string $agreementNumber = '', string $sessionToken = '')
	{
		$this->credentials = $credentials;
		$this->agreementNumber = $agreementNumber;
		$this->sessionToken = $sessionToken;
		$this->httpClient = new httpClient([
			'base_uri' => 'https://' . $credentials->domain,
			'defaults' => [
				'headers' => [
					'Content-Type' => 'application/json',
					'Accept' => 'application/json',
				],
			],
		]);
	}

	/**
	 * Залогиниться получив сессионный токен.
	 *
	 * @return void
	 * @throws Exception
	 * @throws GuzzleException
	 */
	public function login()
	{
		$login = new Login([
			'login' => $this->credentials->login,
			'password' => $this->credentials->password,
		]);
		if (!empty($this->agreementNumber)) {
			$login->agreementNumber = $this->agreementNumber;
		}
		$response = $this->sendRequest($login);
		$body = Json::decode($response->getBody()->getContents(), true);
		if ($response->getStatusCode() != 200) {
			$code = (integer) $body['apiErrorCode'];
			throw new Exception(
				Exception::$codeList[$code] ?? 'Неизвестная ошибка',
				$code ?? 0
			);
		}
		if (empty($body['sessionToken'])) {
			throw new Exception('Не удалось получить сессионный токен');
		}
		$this->sessionToken = $body['sessionToken'];
	}

	/**
	 * Отправить запрос.
	 *
	 * @param Request $endpoint
	 * @return ResponseInterface
	 * @throws GuzzleException
	 * @throws Exception
	 */
	protected function sendRequest(Request $endpoint): ResponseInterface
	{
		return $this->httpClient->request(
			$endpoint->method,
			$endpoint->path,
			[
				'debug' => $endpoint->debug,
				'exceptions' => false,
				'headers' => [
					'Session-Token' => $this->sessionToken,
					'Integrator-ID' => $this->credentials->integratorId,
					'Accept'        => 'application/json',
					'User-Agent'    => 'PHP-OFD-SDK/' . $this->version,
				],
				'query' => $endpoint->getQuery(),
				'json' => $endpoint->getBody(),
			]
		);
	}

	/**
	 * Шлет запрос и обрабатывает результаты.
	 *
	 * @param Request $endpoint
	 * @return array
	 * @throws GuzzleException
	 * @throws Exception
	 * @see https://lk-ofd.taxcom.ru/ApiHelp/index.html?3___.htm Обработка ошибок
	 */
	public function request($endpoint): array
	{
		$response = $this->sendRequest($endpoint);
		$body = Json::decode($response->getBody()->getContents(), true);
		$errorCode = $body['apiErrorCode'] ?? 0;
		// Истек срок действия маркера доступа
		if ($errorCode == 2109) {
			// Обновляем токен
			$this->login();
			$response = $this->sendRequest($endpoint);
			$body = Json::decode($response->getBody()->getContents(), true);
			$errorCode = $body['apiErrorCode'] ?? 0;
		}
		if (!empty($errorCode)) {
			throw new Exception(
				get_class($endpoint) . ' ошибка: ' . ($body['details'] ?? $body['commonDescription']),
				$errorCode
			);
		}
		return $body;
	}
}