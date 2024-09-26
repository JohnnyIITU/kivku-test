<?php
namespace Johnny\Kviku\Dtos;

readonly class CreditDto
{
    public function __construct(
        public string $email,
        public string $ip,
        public string $firstName,
        public string $lastName,
        public float $creditTotal
    ) { }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'ip' => $this->ip,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'credit' => [
                'total' => $this->creditTotal,
            ],
        ];
    }
}