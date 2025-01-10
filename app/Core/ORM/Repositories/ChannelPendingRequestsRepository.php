<?php

namespace SponsorAPI\Core\ORM\Repositories;

use SponsorAPI\Core\ORM\Repositories\BaseRepository;

class ChannelPendingRequestsRepository extends BaseRepository
{
    public const TABLE = 'channel_join_requests';

    public function isUserPendingOnChannelId(int $userId, int $channelId, string $botUsername): bool
    {
        $sql = "SELECT COUNT(user_id) as tot FROM " . self::TABLE . " WHERE bot_username = ? AND user_id = ? AND channel_id = ?";
        $res = $this->db->query($sql, $botUsername, $userId, $channelId)->fetch()['tot'] ?? 0;
        return $res > 0;
    }
}