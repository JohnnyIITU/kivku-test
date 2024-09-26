<?php

namespace Johnny\Kviku\Helpers;

class EnvironmentHelper
{
    public static function loadEnv()
    {
        $env = file_get_contents(__DIR__ . '/../../.env');
        $lines = explode("\n", $env);

        foreach ($lines as $line) {
            preg_match("/([^#]+)\=(.*)/",$line,$matches);
            if(isset($matches[2])){
                putenv(trim($line));
            }
        }
    }
}