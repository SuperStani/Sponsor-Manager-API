<?php

namespace SponsorAPI\Services;

use DateTime;
use SponsorAPI\Core\ORM\Entities\MiniSponsorEntity;
use SponsorAPI\Core\ORM\Repositories\MiniSponsorsRepository;

class MiniSponsorsService
{
    private CacheService $cacheService;
    private MiniSponsorsRepository $miniSponsorsRepository;

    public function __construct(CacheService $cacheService, MiniSponsorsRepository $miniSponsorsRepository)
    {
        $this->cacheService = $cacheService;
        $this->miniSponsorsRepository = $miniSponsorsRepository;
    }


    public function getActiveMiniSponsorByBotUsername(string $bot_username): MiniSponsorEntity|false
    {
        if (($sponsor = $this->cacheService->getMiniSponsor($bot_username)) === false) {
            $sponsor = $this->miniSponsorsRepository->getActiveMiniSponsorByBotUsername($bot_username);
            if ($sponsor) {
                try {
                    $now = new DateTime();
                    $finish = new DateTime($sponsor->getDatetimeStop());
                } catch (\Exception $e) {
                    return false;
                }

                $interval = $now->diff($finish);
                $seconds = ($interval->days * 24 * 60 * 60) +
                    ($interval->h * 60 * 60) +
                    ($interval->i * 60) +
                    $interval->s;
                $sponsor->setLifeTimeSeconds((int)$seconds / 2);
                $this->cacheService->saveMiniSponsor($sponsor);
                return $sponsor;
            }
            return false;
        }
        return $sponsor;
    }
}