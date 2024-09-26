<?php

namespace Johnny\Kviku\Services;

use GuzzleHttp\Exception\GuzzleException;
use Johnny\Kviku\Clients\KvikuClient;
use Johnny\Kviku\Dtos\CreditDto;
use Johnny\Kviku\Values\CreditInfoValue;
use Johnny\Kviku\Values\UserValue;
use Psr\Http\Message\ResponseInterface;

class KvikuService
{
    private const BYTES_TO_READ = 1000000; // 1MB
    private KvikuClient $kvikuClient;
    public function __construct()
    {
        $this->kvikuClient = new KvikuClient('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJlbWFpbCI6InByaW1lLmpvaG5ueTk4QGdtYWlsLmNvbSIsImV4cCI6MTcyNzk1NDc3MiwiZmlyc3ROYW1lIjoiWmhhbmliZWsiLCJsYXN0TmFtZSI6IkF6aGltb3YifQ.YrNydbOEamVxijAoR5TzZiwPt9LMRSXNLw0WowTFUSI', 'https://php-test.dev.kviku.space');
    }

    /**
     * @throws \ValidationException
     * @throws GuzzleException
     * @throws \ElementNotFoundException
     */
    public function handle(): void
    {
        $creditInfo = $this->collectData();

        $total = round($this->calculateTotal($creditInfo->user->creditAmount, $creditInfo->awaitCreditDays, $creditInfo->awaitCreditPercentPerDay), 2);

        $this->sendData(new CreditDto(
            $creditInfo->user->email,
            $creditInfo->user->ip,
            $creditInfo->user->firstName,
            $creditInfo->user->lastName,
            $total,
        ));
    }

    /**
     * @throws \ValidationException
     * @throws \ElementNotFoundException|GuzzleException
     */
    public function collectData(): CreditInfoValue
    {
        $response = $this->kvikuClient->getData();

        $awaitCreditDays = (int)$response->getHeader('Await-Credit-Days')[0];
        $awaitCreditPercentPerDay = (float)$response->getHeader('Await-Credit-Percent-Per-Day')[0];
        $awaitElementNumber = (int)$response->getHeader('Await-Element-Number')[0];

        $userData = $this->getUser($response, $awaitElementNumber);

        if ($userData === null) {
            throw new \Exception('User data not found');
        }

        return new CreditInfoValue(
            UserValue::fromArray($userData),
            $awaitCreditDays,
            $awaitCreditPercentPerDay,
            $awaitElementNumber
        );
    }

    /**
     * @throws GuzzleException
     */
    public function sendData(CreditDto $dto): void
    {
        $response = $this->kvikuClient->sendData($dto->toArray());

        // TODO check for success response
    }

    /**
     * @throws \ElementNotFoundException
     */
    private function getUser(ResponseInterface $response, int $awaitElementNumber): ?array
    {
        $body = $response->getBody();
        $buffer = '';
        $count = 0;

        while (!$body->eof()) {
            $buffer .= $body->read(self::BYTES_TO_READ);

            $countInBuffer = substr_count($buffer, '}}');

            if ($countInBuffer + $count > $awaitElementNumber) {
                $start = strpos($buffer, '{"email"');
                $end = strrpos($buffer, '}}');

                $json = '[' . substr($buffer, $start, $end + 1) . ']';
                $res = json_decode($json, true);

                if (!$res) {
                    $json = '[' . substr($buffer, $start, $end + 2) . ']';
                    $res = json_decode($json, true);
                }

                if (!isset($res[$awaitElementNumber - $count])) {
                    throw new \ElementNotFoundException();
                }

                return $res[$awaitElementNumber - $count];
            } else {
                $count += $countInBuffer;
                $lastPos = strrpos($buffer, '}},') + 3;
                $buffer = substr($buffer, $lastPos);
            }
        }

        return null;
    }

    private function calculateTotal(int $initialSum, int $creditDays, float $creditPercent): float
    {
        $n = 1 + $creditPercent / 100;

        var_dump([
            'initialSum' => $initialSum,
            'creditDays' => $creditDays,
            'creditPercent' => $creditPercent,
            'total' => $initialSum * pow($n, $creditDays),
        ]);

        return round($initialSum * pow($n, $creditDays), 2);
    }
}