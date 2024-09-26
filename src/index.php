<?php

require_once "../vendor/autoload.php";

use Johnny\Kviku\Helpers\EnvironmentHelper;
use Johnny\Kviku\Services\KvikuService;

EnvironmentHelper::loadEnv();

$service = new KvikuService();

$service->handle();