<?php


namespace SponsorAPI\Core\Controllers;

use SponsorAPI\Core\ORM\Entities\MiniSponsorEntity;
use SponsorAPI\Services\ChannelsService;
use SponsorAPI\Services\MiniSponsorsService;

class APIController
{
    private ?string $action;

    private array $response;

    private ChannelsService $channelsService;

    private MiniSponsorsService $miniSponsorsService;

    public function __construct(ChannelsService $channelsService, MiniSponsorsService $miniSponsorsService)
    {
        $this->response = [
            "result" => false,
            "message" => "Bad request"
        ];
        $this->channelsService = $channelsService;
        $this->miniSponsorsService = $miniSponsorsService;
    }

    public function init(): void
    {
        header("Content-Type: application/json");
        $this->action = $_GET['action'] ?? null;
    }

    public function process(): void
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
                break;
            case 'getMiniSponsor':
                $this->getMiniSponsor();
        }
    }

    private function addChannel()
    {

    }

    private function checkUser(): void
    {
        if (isset($_GET['bot_username'], $_GET['user_id'])) {
            $channelsData = $this->channelsService->getInviteUrls($_GET['bot_username']);
            if (is_array($channelsData)) {
                $data = ["result" => true, "user_id" => $_GET['user_id'], "channels" => [], "is_subscribed_all" => true];
                foreach ($channelsData as $channel) {
                    $is_subscribed = $this->channelsService->checkUserOnChannel($channel['channel_id'], $_GET['user_id']);
                    $data['channels'][] = [
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

    private function addJoinedUser(): void
    {
        if (isset($_GET['invite_url'], $_GET['user_id'])) {
            if ($this->channelsService->addJoinedUser(rawurldecode($_GET['invite_url']), $_GET['user_id'])) {
                $this->response['result'] = true;
                $this->response['message'] = "User has registered in statistics";
            }
        }
    }

    private function getMiniSponsor(): void
    {
        if (isset($_GET['bot_username'])) {
            if (($sponsor = $this->miniSponsorsService->getActiveMiniSponsorByBotUsername($_GET['bot_username'])) !== false) {
                $this->response['result'] = true;
                $this->response['message'] = "Result found";
                $this->response['sponsor'] = [
                    'message' => $sponsor->getMessage(),
                    'datetime_stop' => $sponsor->getDatetimeStop()
                ];
            } else {
                $this->response['result'] = false;
                $this->response['message'] = "No Sponsor active found";
            }
        }
    }

    public function response(): void
    {
        echo json_encode($this->response);
    }
}