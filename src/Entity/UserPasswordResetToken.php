<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Repository\UserPasswordResetTokenRepository;
use Doctrine\ORM\Mapping as ORM;
use \DateTimeImmutable;
use Symfony\Component\String\ByteString;

#[ORM\Entity(repositoryClass: UserPasswordResetTokenRepository::class)]
class UserPasswordResetToken
{
    use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255, unique: true, nullable: false)]
    private string $token;

    #[ORM\Column]
    private DateTimeImmutable $expiresAt;

    public function __construct()
    {
        $this->token = ByteString::fromRandom(100);
        $this->expiresAt = new DateTimeImmutable('+1 hour');
    }

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

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new DateTimeImmutable();
    }
}
