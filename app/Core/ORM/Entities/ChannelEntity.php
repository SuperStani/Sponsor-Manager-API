<?php


namespace SponsorAPI\Core\ORM\Entities;


class ChannelEntity
{
    private ?int $id;
    private int $chatId;
    private ?string $title;
    private string $inviteUrl;
    private ?int $members;

    public function __construct(int $chatId, string $inviteUrl, ?int $members = null, ?string $title = null, ?int $id = null)
    {
        $this->chatId = $chatId;
        $this->title = $title;
        $this->inviteUrl = $inviteUrl;
        $this->members = $members;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getChatId(): int
    {
        return $this->chatId;
    }

    /**
     * @param int $chatId
     */
    public function setChatId(int $chatId): void
    {
        $this->chatId = $chatId;
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
     * @param string $inviteUrl
     */
    public function setInviteUrl(string $inviteUrl): void
    {
        $this->inviteUrl = $inviteUrl;
    }

    /**
     * @return int
     */
    public function getMembers(): int
    {
        return $this->members;
    }

    /**
     * @param int $members
     */
    public function setMembers(int $members): void
    {
        $this->members = $members;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

}