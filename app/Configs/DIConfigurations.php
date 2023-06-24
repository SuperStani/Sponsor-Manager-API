<?php

use \SponsorAPI\Core\Integration\TelegramBotApi;
use \SponsorAPI\Configs\GeneralConfigurations;
use \SponsorAPI\Configs\RedisConfigurations;
use \SponsorAPI\Configs\DatabaseCredentials;
use \SponsorAPI\Core\Controllers\RedisController;
use \SponsorAPI\Core\ORM\DB;

use \GuzzleHttp\Client;
use \Psr\Container\ContainerInterface;
use function DI\factory;

return [
    TelegramBotApi::class => factory(function (ContainerInterface $c) {
        return new TelegramBotApi(
            $c->get(Client::class),
            GeneralConfigurations::BOT_TOKEN,
            GeneralConfigurations::TELEGRAM_API_URL
        );
    }),
    DB::class => factory(function () {
        return new DB(
            DatabaseCredentials::HOST,
            DatabaseCredentials::PORT,
            DatabaseCredentials::USER,
            DatabaseCredentials::PASSWORD,
            DatabaseCredentials::DATABASE
        );
    }),
    RedisController::class => factory(function () {
        return new RedisController(
            RedisConfigurations::HOST,
            RedisConfigurations::PORT,
            RedisConfigurations::SOCKET
        );
    })
];