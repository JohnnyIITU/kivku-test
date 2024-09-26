<?php

require_once "../vendor/autoload.php";

use Johnny\Kviku\Services\KvikuService;

$service = new KvikuService();

$service->handle();