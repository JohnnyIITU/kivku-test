<?php

class ValidationException extends Exception
{
    public const ERROR_REQUIRED = 'required';
    public function __construct(string $field, string $error)
    {
        parent::__construct("Validation error: $field: $error", 400);
    }
}