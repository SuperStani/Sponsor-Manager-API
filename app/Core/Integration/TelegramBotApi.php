<?php


namespace SponsorAPI\Core\Integration;

use Exception;
use GuzzleHttp\Client;

class TelegramBotApi
{
    private string $bot_token;
    private string $api_url;
    private Client $request;

    public function __construct(Client $request, string $bot_token, string $api_url)
    {
        $this->bot_token = $bot_token;
        $this->api_url = $api_url;
        $this->request = $request;
    }

    public function checkUserSubscribedOnChannel(int $user_id, int $channel_id): bool
    {
        $response = $this->apiRequest('getChatMember', ["chat_id" => $channel_id, "user_id" => $user_id]);
        if ($response !== null) {
            if ($response['ok'] == true && $response['result']['status'] !== 'left') {
                return true;
            }
        }
        return false;
    }

    public function getChatIdMembers(int $chat_id): int
    {
        $response = $this->apiRequest('getChatMemberCount', ["chat_id" => $chat_id]);
        if($response !== null) {
            return $response['result'];
        }
        return 0;
    }

    private function apiRequest(string $method, array $args): ?string
    {
        $options = [
            "connect_timeout" => 2,
            "timeout" => 2
        ];
        $query_params = http_build_query($args);
        $url = $this->api_url . $this->bot_token . "/" . $method . "?" . $query_params;
        try {
            $res = $this->request->get($url, $options);
            if ($res->getStatusCode() == 200) {
                return json_decode($res->getBody(), true);
            }
        } catch (Exception $e) {
        }
        return null;
    }
}