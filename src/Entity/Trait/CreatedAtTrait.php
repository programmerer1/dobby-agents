<?php

declare(strict_types=1);

namespace App\Entity\Trait;

use DateTimeImmutable;
use Gedmo\Mapping\Annotation\Timestampable;
use Doctrine\ORM\Mapping as ORM;

trait CreatedAtTrait
{
    #[Timestampable(on: 'create')]
    #[ORM\Column(name: 'created_at')]
    private ?DateTimeImmutable $createdAt = null;

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
