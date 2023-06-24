<?php


namespace SponsorAPI\Core\ORM\Repositories;


use SponsorAPI\Core\ORM\DB;

class ChannelsRepository
{
    private DB $db;

    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    public function getInviteUrlsByBotUsername(string $bot_username): array
    {
        $query = "
            SELECT 
                   channels.channel_id, 
                   ciu.invite_url
            FROM (
                     SELECT c.channel_id, c.datetime
                     FROM channels c
                     WHERE c.users_range IS NOT NULL
                       AND c.users_range <= c.earned_users
                     UNION
                     SELECT c1.channel_id, c1.datetime
                     FROM channels c1
                     WHERE c1.datetime_start < NOW()
                       AND c1.datetime_stop > NOW()
                     ORDER BY datetime -- Add an ORDER BY clause to specify the desired order
                     LIMIT 5 -- Limit the results in the subquery
                 ) channels
            LEFT JOIN channels_invite_urls ciu
            ON ciu.channel_id = channels.channel_id
            ORDER by channels.datetime LIMIT 5
        ";
        $res = $this->db->query($query, $bot_username);
        $data = [];
        foreach ($res as $row) {
            $data[] = ['channel_id' => $row['channel_id'], "invite_url" => $row['invite_url']];
        }
        return $data;
    }
}