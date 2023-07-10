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
                break;
            case 'addJoinedUser':
                $this->addJoinedUser();
        }
    }

    private function addChannel()
    {

    }

    private function checkUser()
    {
        if (isset($_GET['bot_username'], $_GET['user_id'])) {
            $channelsData = $this->channelsService->getInviteUrls($_GET['bot_username']);
            if (is_array($channelsData)) {
                $data = ["result" => true, "user_id" => $_GET['user_id'], "channels" => [], "is_subscribed_all" => true];
                foreach ($channelsData as $channel) {
                    $is_subscribed = $this->channelsService->checkUserOnChannel($channel['channel_id'], $_GET['user_id']);
                    $data['channels'] = [
                        'id' => $channel['channel_id'],
                        "is_subscribed" => $is_subscribed,
                        "invite_url" => $channel['invite_url']
                    ];
                    if (!$is_subscribed) {
                        $data['is_subscribed_all'] = false;
                    }
                }
                $this->response = $data;
            }
        }
    }

    private function addJoinedUser()
    {
        if (isset($_GET['invite_url'], $_GET['user_id'])) {
            if ($this->channelsService->addJoinedUser($_GET['invite_url'], $_GET['user_id'])) {
                $this->response['result'] = true;
                $this->response['message'] = "User has registered in statistics";
            }
        }
    }

    public function response(): void
    {
        echo json_encode($this->response);
    }
}