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
        SELECT channels.channel_id,
               channels.invite_url,
               channels.bot_username
        FROM (
                 SELECT c.channel_id, c.invite_url, c.datetime, c.bot_username
                 FROM channels c
                 WHERE c.users_range IS NOT NULL
                   AND c.users_range <= c.earned_users
                 UNION
                 SELECT c1.channel_id, c1.invite_url, c1.datetime, c1.bot_username
                 FROM channels c1
                 WHERE c1.datetime_start < NOW()
                   AND c1.datetime_stop > NOW()
                 ORDER BY datetime -- Add an ORDER BY clause to specify the desired order
                 LIMIT 5 -- Limit the results in the subquery
             ) channels
        WHERE channels.bot_username = ?
        ORDER by channels.datetime
        LIMIT 5
        ";
        $res = $this->db->query($query, $bot_username);
        if ($res) {
            $data = [];
            foreach ($res as $row) {
                $data[] = ['channel_id' => $row['channel_id'], "invite_url" => $row['invite_url']];
            }
            return $data;
        }
        return [];
    }
}