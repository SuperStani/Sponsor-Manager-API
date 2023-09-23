<?php


namespace SponsorAPI\Core\ORM\Repositories;


use PDO;
use SponsorAPI\Core\ORM\DB;
use SponsorAPI\Core\ORM\Entities\ChannelEntity;

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
                   AND c.users_range > c.earned_users
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
            return $res->fetchAll(PDO::FETCH_CLASS, ChannelEntity::class);
            $data = [];
            foreach ($res as $row) {
                $data[] = ['channel_id' => $row['channel_id'], "invite_url" => $row['invite_url']];
            }
            return $data;
        }
        return [];
    }

    public function addJoinedUser(string $invite_link, int $user_id): ?\PDOStatement
    {
        $sql = "UPDATE channels SET earned_users = earned_users + 1 WHERE invite_url = ?";
        return $this->db->query($sql, $invite_link);
    }


    public function getActiveSponsor(): array
    {
        $query = "
        SELECT 
               channels.sponsor_id,
               channels.channel_id,
               channels.invite_url,
               channels.bot_username
        FROM (
                 SELECT c.id as sponsor_id, c.channel_id, c.invite_url, c.datetime, c.bot_username
                 FROM channels c
                 WHERE c.users_range IS NOT NULL
                   AND c.users_range > c.earned_users
                 UNION
                 SELECT c1.id as sponsor_id, c1.channel_id, c1.invite_url, c1.datetime, c1.bot_username
                 FROM channels c1
                 WHERE c1.datetime_start < NOW()
                   AND c1.datetime_stop > NOW()
                 ORDER BY datetime -- Add an ORDER BY clause to specify the desired order
                 LIMIT 5 -- Limit the results in the subquery
             ) channels
        ORDER by channels.sponsor_id, channels.datetime
        LIMIT 5
        ";
        $res = $this->db->query($query);
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