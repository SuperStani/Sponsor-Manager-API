<?php

use SponsorAPI\Services\ChannelsService;

$container = require_once __DIR__ . "/../bootstrap.php";

$channelsService = $container->get(ChannelsService::class);
$channelsService->updateEarnedUsers();