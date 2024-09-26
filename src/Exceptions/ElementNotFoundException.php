<?php

class ElementNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Element not found', 404);
    }
}