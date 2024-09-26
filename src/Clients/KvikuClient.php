<?php

namespace Johnny\Kviku\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Johnny\Kviku\Enums\HttpMethod;
use Psr\Http\Message\ResponseInterface;

class KvikuClient implements KvikuClientInterface
{
    private const ACTION_PATH = 'api/v1/task';
    public function __construct(
        private readonly string $token,
        private readonly string $baseUrl,
    ) {}

    /**
     * @throws GuzzleException
     */
    public function getData(): ResponseInterface
    {
        return $this->sendRequest(
            method: HttpMethod::GET,
            path: self::ACTION_PATH,
            isStreamResponse: true
        );
    }

    /**
     * @throws GuzzleException
     */
    public function sendData(array $data): ResponseInterface
    {
        return $this->sendRequest(
            method: HttpMethod::POST,
            path: self::ACTION_PATH,
            params: $data,
        );
    }

    /**
     * @throws GuzzleException
     */
    private function sendRequest(HttpMethod $method, string $path, array $params = [], bool $isStreamResponse = false): ResponseInterface
    {
        $client = new Client(['base_uri' => $this->baseUrl, 'stream' => $isStreamResponse, 'debug' => true]);

        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ];

        if ($method === HttpMethod::POST) {
            $options['json'] = $params;
        }

        return $client->request($method->value, $path, $options);
    }
}