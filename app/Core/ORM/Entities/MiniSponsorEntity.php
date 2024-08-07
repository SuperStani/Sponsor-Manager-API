<?php

namespace SponsorAPI\Core\ORM\Entities;

class MiniSponsorEntity
{
    private int $id;

    private string $bot_username;

    private string $message;

    private string $datetime_stop;

    private string $datetime_start;

    private int $life_time_seconds;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getBotUsername(): string
    {
        return $this->bot_username;
    }

    public function setBotUsername(string $bot_username): void
    {
        $this->bot_username = $bot_username;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getDatetimeStop(): string
    {
        return $this->datetime_stop;
    }

    public function setDatetimeStop(string $datetime_stop): void
    {
        $this->datetime_stop = $datetime_stop;
    }

    public function getDatetimeStart(): string
    {
        return $this->datetime_start;
    }

    public function setDatetimeStart(string $datetime_start): void
    {
        $this->datetime_start = $datetime_start;
    }

    public function getLifeTimeSeconds(): int
    {
        return $this->life_time_seconds;
    }

    public function setLifeTimeSeconds(int $life_time_seconds): void
    {
        $this->life_time_seconds = $life_time_seconds;
    }


}