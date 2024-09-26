<?php

namespace Johnny\Kviku\Values;

class UserValue
{
    public function __construct(
        public readonly string $email,
        public readonly string $ip,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $creditCurrency,
        public readonly string $creditAmount,
    ){}

    /**
     * @throws \ValidationException
     */
    public static function fromArray(array $array): static
    {
        return new self(
            $array['email'],
            $array['ip'],
            $array['firstName'],
            $array['lastName'],
            $array['credit']['currency'],
            $array['credit']['amount'],
        );
    }
}