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
                   AND c.bot_username = ?
             ) channels
        WHERE channels.bot_username = ?
        ORDER by channels.sponsor_id, channels.datetime
        LIMIT 8
        ";
        $res = $this->db->query($query, $bot_username, $bot_username);
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
                       channels.sponsor_id,
                       channels.channel_id,
                       channels.invite_url,
                       channels.total_members
                FROM (
                         SELECT c.id as sponsor_id, c.channel_id, c.invite_url, c.datetime, c.total_members
                         FROM channels c
                         WHERE c.users_range IS NOT NULL
                           AND c.users_range > c.earned_users
                     ) channels
                ORDER by channels.sponsor_id, channels.datetime
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

    public function updateChannelUsers(ChannelEntity $channelEntity, $users): void
    {
        $earned = ($channelEntity->getMembers() > 0 && ($total = $users - $channelEntity->getMembers()) > 0) ? $total : 0;
        $sql = "UPDATE channels SET total_members = ? WHERE id = ?";
        $this->db->query($sql, $users, $channelEntity->getId());
    }
}