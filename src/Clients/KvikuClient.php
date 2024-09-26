<?php

namespace Johnny\Kviku\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class KvikuClient implements KvikuClientInterface
{
    private Client $client;
    public function __construct(
        private readonly string $token,
        private readonly string $baseUrl,
    ) {
        $this->client = new Client(['base_uri' => $baseUrl, 'stream' => true, 'debug' => true]);
    }

    /**
     * @throws GuzzleException
     */
    public function getData(): ResponseInterface
    {
        return $this->client->request('GET', 'api/v1/task', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function sendData(array $data): ResponseInterface
    {
        $client = new Client();

        return $client->post(rtrim($this->baseUrl, '/\\') . '/api/v1/task', [
            'json' => $data,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ]);
    }
}