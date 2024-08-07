<?php


namespace SponsorAPI\Services;

use SponsorAPI\Configs\GeneralConfigurations;
use SponsorAPI\Core\Controllers\RedisController;
use SponsorAPI\Core\ORM\Entities\MiniSponsorEntity;

class CacheService
{
    private RedisController $redisController;

    public function __construct(RedisController $redisController)
    {
        $this->redisController = $redisController;
    }

    public function saveInviteUrls(string $bot_username, mixed $value): bool
    {
        try {
            $this->redisController->setKey(GeneralConfigurations::APP_PREFIX . "_INVITE_URLS_" . $bot_username, json_encode($value), 5);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getInviteUrls(string $bot_username): array|bool
    {
        try {
            return json_decode($this->redisController->getKey(GeneralConfigurations::APP_PREFIX . "_INVITE_URLS_" . $bot_username), true) ?? false;
        } catch (\Exception $e) {

        }
        return false;
    }

    public function saveMiniSponsor(MiniSponsorEntity $sponsor): bool
    {
        try {
            $this->redisController->setKey(GeneralConfigurations::APP_PREFIX . "_MINI_SPONSOR_" . $sponsor->getBotUsername(), serialize($sponsor), $sponsor->getLifeTimeSeconds());
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getMiniSponsor(string $bot_username): MiniSponsorEntity|false
    {
        try {
            return unserialize($this->redisController->getKey(GeneralConfigurations::APP_PREFIX . "_MINI_SPONSOR_" . $bot_username)) ?? false;
        } catch (\Exception $e) {

        }
        return false;
    }
}