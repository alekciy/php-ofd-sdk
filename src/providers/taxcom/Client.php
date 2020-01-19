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

	/** @var int Номер запроса */
	static private $requestNumber = 0;

	/** @var string Директория куда будет сохраняться тело ответа */
	static private $bodyStorageDir = '';

	/**
	 * @param Credentials $credentials Реквизиты доступа.
	 * @param string $agreementNumber  Номер договора.
	 * @param string $sessionToken     Сессионный токен доступа.
	 * @param array $clientConfig      Дополнительную настройки для Guzzle клиента.
	 * @throws Exception
	 */
	public function __construct(Credentials $credentials, string $agreementNumber = '', string $sessionToken = '', array $clientConfig = [])
	{
		$this->credentials = $credentials;
		$this->agreementNumber = $agreementNumber;
		$this->sessionToken = $sessionToken;

		$httpClientConfig = [
			'base_uri' => 'https://' . $credentials->domain,
			'defaults' => [
				'headers' => [
					'Content-Type' => 'application/json',
					'Accept' => 'application/json',
				],
			],
		];
		$this->httpClient = new httpClient(array_merge($clientConfig, $httpClientConfig));
	}

	/**
	 * Задает директорию куда будут сохраняться тела всех ответов. Метод полезен при создании фикстур для тестов.
	 *
	 * @param string $dirPath
	 * @return void
	 * @throws Exception
	 */
	static public function setBodyStorageDir(string $dirPath)
	{
		$absDirPath = realpath($dirPath);
		if ($absDirPath === false
			|| !is_writable($absDirPath)
		) {
			throw new Exception("Директория «{$absDirPath}» недоступна для записи");
		}
		self::$bodyStorageDir = $absDirPath;
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
				\alekciy\ofd\providers\taxcom\Exception::$codeList[$code] ?? 'Неизвестная ошибка',
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
		$response =  $this->httpClient->request(
			$endpoint->method,
			$endpoint->getPath(),
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
		++self::$requestNumber;
		if (!empty(self::$bodyStorageDir)) {
			$fileName = sprintf('%05d_%s', self::$requestNumber, str_replace('/', '_', $endpoint->getPath()) . '.json');
			$body = (string) $response->getBody()->getContents();
			$response->getBody()->rewind();
			file_put_contents(self::$bodyStorageDir . '/' . $fileName, $body . PHP_EOL);
		}

		return $response;
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