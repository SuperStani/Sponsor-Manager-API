<?php

namespace SponsorAPI\Core\ORM\Repositories;

use SponsorAPI\Core\ORM\Entities\MiniSponsorEntity;

class MiniSponsorsRepository extends BaseRepository
{
    public const TABLE = 'mini_sponsors';

    public function getActiveMiniSponsorByBotUsername(string $bot_username): MiniSponsorEntity|null
    {
        $sql = "
            SELECT 
                id, 
                bot_username, 
                message, 
                datetime_start, 
                datetime_stop 
            WHERE 
                bot_username = ?
                AND datetime_start <= NOW() 
                AND datetime_stop > NOW() 
            LIMIT 1
        ";
        $res = $this->db->query($sql, $bot_username)->fetch();
        if ($res['id']) {
            $sponsor = new MiniSponsorEntity();
            $sponsor->setId($res['id']);
            $sponsor->setBotUsername($res['bot_username']);
            $sponsor->setMessage($res['message']);
            $sponsor->setDatetimeStop($res['datetime_stop']);
            $sponsor->setDatetimeStart($res['datetime_start']);
            return $sponsor;
        }
        return null;
    }
}