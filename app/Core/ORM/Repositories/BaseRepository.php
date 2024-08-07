<?php

namespace SponsorAPI\Core\ORM\Repositories;

use SponsorAPI\Core\ORM\DB;

abstract class BaseRepository
{
    protected DB $db;

    public function __construct(DB $db)
    {
        $this->db = $db;
    }
}