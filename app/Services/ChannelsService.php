<?php


namespace SponsorAPI\Services;


use SponsorAPI\Core\Integration\TelegramBotApi;
use SponsorAPI\Core\ORM\Repositories\ChannelsRepository;

class ChannelsService
{
    private ChannelsRepository $channelsRepository;
    private TelegramBotApi $telegramBotApi;
    private CacheService $cacheService;

    public function __construct(ChannelsRepository $channelsRepository, TelegramBotApi $telegramBotApi, CacheService $cacheService)
    {
        $this->channelsRepository = $channelsRepository;
        $this->telegramBotApi = $telegramBotApi;
        $this->cacheService = $cacheService;
    }

    public function getInviteUrls(string $bot_username): array|bool
    {
        if (($data = $this->cacheService->getInviteUrls(bot_username: $bot_username)) === false) {
            $res = $this->channelsRepository->getInviteUrlsByBotUsername(bot_username: $bot_username);
            $data = [];
            foreach ($res as $row) {
                $data[] = ['channel_id' => $row['channel_id'], "invite_url" => $row['invite_url']];
            }
            $this->cacheService->saveInviteUrls(bot_username: $bot_username, value: $data);
        }
        return $data;
    }

    public function checkUserOnChannel(int $channel_id, int $user_id): bool
    {
        return $this->telegramBotApi->checkUserSubscribedOnChannel(user_id: $user_id, channel_id: $channel_id);
    }

    public function addJoinedUser(string $invite_link, int $user_id): bool
    {
        return $this->channelsRepository->addJoinedUser($invite_link, $user_id) !== null;
    }

    public function updateEarnedUsers()
    {
        //$this->channelsRepository->
    }

}