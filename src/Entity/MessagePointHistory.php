<?php

namespace App\Entity;

use App\Repository\MessagePointHistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: MessagePointHistoryRepository::class)]
class MessagePointHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'messagePointHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToOne(inversedBy: 'messagePointHistory', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Message $message = null;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0, 'unsigned' => true])]
    private int $points = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(Message $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }
}
