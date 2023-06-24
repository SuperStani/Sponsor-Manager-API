<?php

use \SponsorAPI\Core\Controllers\APIController;

$container = require_once __DIR__ . "/../bootstrap.php";

$api = $container->get(APIController::class);

$api->init();
$api->process();
$api->response();