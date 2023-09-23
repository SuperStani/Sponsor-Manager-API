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

    public function getInviteUrlsByBotUsername(string $bot_username): ?\Generator
    {
        $query = "
        SELECT 
               channels.channel_id,
               channels.invite_url
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
        WHERE channels.bot_username = ?
        ORDER by channels.sponsor_id, channels.datetime
        LIMIT 5
        ";
        $res = $this->db->query($query, $bot_username);
        if ($res) {
            foreach ($res as $row) {
                yield new ChannelEntity($row['channel_id'], $row['invite_url']);
            }
        }
        return null;
    }

    public function addJoinedUser(string $invite_link, int $user_id): ?\PDOStatement
    {
        $sql = "UPDATE channels SET earned_users = earned_users + 1 WHERE invite_url = ?";
        return $this->db->query($sql, $invite_link);
    }


    public function getActiveSponsors(): ?\Generator
    {
        $query = "
                SELECT
                       channels.sposnor_id,
                       channels.channel_id,
                       channels.invite_url,
                       channels.total_members
                FROM (
                         SELECT c.id as sponsor_id, c.channel_id, c.invite_url, c.datetime, c.total_members
                         FROM channels c
                         WHERE c.users_range IS NOT NULL
                           AND c.users_range > c.earned_users
                         UNION
                         SELECT c1.id as sponsor_id, c1.channel_id, c1.invite_url, c1.datetime, c1.total_members
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
            foreach ($res as $row) {
                yield new ChannelEntity(
                    chatId: $row['channel_id'],
                    inviteUrl: $row['invite_url'],
                    members: $row['total_members'],
                    id: $row['sponsor_id']
                );
            }
        }
        return null;
    }

    public function updateChannelUsers(ChannelEntity $channelEntity, $users)
    {
        $earned = ($total = $users - $channelEntity->getMembers()) > 0 ? $total : 0;
        $sql = "UPDATE channels SET earned_users = earned_users + ?, total_members = ? WHERE id = ?";
        $this->db->query($sql, $earned, $users, $channelEntity->getId());
    }
}