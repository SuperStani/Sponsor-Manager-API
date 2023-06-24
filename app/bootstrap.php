<?php

use DI\ContainerBuilder;

require_once __DIR__ . "/../vendor/autoload.php";

$container = new ContainerBuilder();
$DIConfigs = require_once __DIR__ . "/Configs/DIConfigurations.php";
$container->addDefinitions($DIConfigs);
$container->build();
return $container;
