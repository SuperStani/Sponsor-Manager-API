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
                $sponsor->setLifeTimeSeconds($bot_username == 'bestreaming_bot' ? 30 : 300);
                $this->cacheService->saveMiniSponsor($sponsor);
                return $sponsor;
            }
            return false;
        }
        return $sponsor;
    }
}