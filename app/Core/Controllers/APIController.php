<?php


namespace SponsorAPI\Core\Controllers;

use SponsorAPI\Services\ChannelsService;

class APIController
{
    private ?string $action;

    private array $response;

    private ChannelsService $channelsService;

    public function __construct(ChannelsService $channelsService)
    {
        $this->response = [
            "result" => false,
            "message" => "Bad request"
        ];
        $this->channelsService = $channelsService;
    }

    public function init()
    {
        header("Content-Type: application/json");
        $this->action = $_GET['action'] ?? null;
    }

    public function process()
    {
        switch ($this->action) {
            case 'addChannel':
                $this->addChannel();
                break;

            case 'checkUser':
                $this->checkUser();
        }
    }

    private function addChannel()
    {

    }

    private function checkUser()
    {
        if (isset($_GET['bot_username'], $_GET['user_id'])) {
            $channelsData = $this->channelsService->getInviteUrls($_GET['bot_username']);
            if(is_array($channelsData)) {
                $data = ["result" => true, "user_id" => $_GET['user_id'], "channels" => [], "is_subscribed_all" => true];
                foreach ($channelsData as $channel) {
                    $is_subscribed = $this->channelsService->checkUserOnChannel($channel['channel_id'], $_GET['user_id']);
                    $data['channels'] = ['id' => $channel['channel_id'], "is_subscribed" => $is_subscribed];
                    if (!$is_subscribed) {
                        $data['is_subscribed_all'] = false;
                    }
                }
                $this->response = $data;
            }
        }
    }

    public function response(): void
    {
        echo json_encode($this->response);
    }
}