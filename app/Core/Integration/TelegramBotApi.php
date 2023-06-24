<?php


namespace SponsorAPI\Core\Integration;


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
        $response = json_decode($this->apiRequest('d', ["channel_id" => $channel_id, "user_id" => $user_id]));
        if($response !== null) {
            return true;
        }
        return false;
    }

    private function apiRequest(string $method, mixed...$args): ?string
    {
        $options = [
            "connect_timeout" => 2,
            "timeout" => 5
        ];
        $query_params = http_build_query($args);
        $url = $this->api_url . $this->bot_token . "/" . $method . "?" . $query_params;
        $res = $this->request->get($url, $options);
        if($res->getStatusCode() == 200) {
            return $res->getBody();
        }
        return null;
    }
}