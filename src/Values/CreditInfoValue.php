<?php

namespace Johnny\Kviku\Values;

readonly class CreditInfoValue
{
    public function __construct(
        public UserValue $user,
        public int $awaitCreditDays,
        public float $awaitCreditPercentPerDay,
        public int $awaitElementNumber,
    ){}
}