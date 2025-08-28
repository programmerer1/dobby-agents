<?php

namespace App\Entity;

use App\Repository\AgentAccessRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Entity(repositoryClass: AgentAccessRepository::class)]
#[UniqueConstraint(name: 'unique_agent_and_user_access', columns: ['agent_id', 'user_id'])]
class AgentAccess
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'agentAccesses')]
    #[ORM\JoinColumn(name: 'agent_id', nullable: false, onDelete: 'RESTRICT')]
    private ?Agent $agent = null;

    #[ORM\ManyToOne(inversedBy: 'agentAccesses')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false, onDelete: 'RESTRICT')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): static
    {
        $this->agent = $agent;

        return $this;
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
}
