<?php

namespace alekciy\ofd\providers\yandex;

use alekciy\ofd\Json;
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

	/** @var string Версия клиента */
	private $version = '0.1.0';

	/**
	 * @param Credentials $credentials Реквизиты доступа.
	 * @throws Exception
	 */
	public function __construct(Credentials $credentials)
	{
		$this->credentials = $credentials;
		$this->httpClient = new httpClient([
			'base_uri' => 'https://' . $credentials->domain,
			'defaults' => [
				'headers' => [
					'Content-Type' => 'application/json',
					'Accept'       => 'application/json',
					'X-Yandex-Key' => $credentials->authenticationKey,
					'X-OFD-Key'    => $credentials->authorizationKey,
				],
			],
		]);
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
					'Accept'       => 'application/json',
					'User-Agent'   => 'PHP-OFD-SDK/' . $this->version,
					'X-Yandex-Key' => $this->credentials->authenticationKey,
					'X-OFD-Key'    => $this->credentials->authorizationKey,
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

		return $body;
	}
}