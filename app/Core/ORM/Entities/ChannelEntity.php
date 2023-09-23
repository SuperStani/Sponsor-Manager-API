<?php


namespace SponsorAPI\Core\ORM\Entities;


class ChannelEntity
{
    private int $id;
    private ?string $title;
    private string $inviteUrl;

    public function __construct(int $id, string $inviteUrl, ?string $title = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->inviteUrl = $inviteUrl;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getInviteUrl(): ?string
    {
        return $this->inviteUrl;
    }

    /**
     * @param string|null $inviteUrl
     */
    public function setInviteUrl(?string $inviteUrl): void
    {
        $this->inviteUrl = $inviteUrl;
    }
}