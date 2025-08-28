<?php

namespace App\Entity;

use App\Repository\AgentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AgentRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'This username is already taken.')]
class Agent
{
    use Trait\CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $username = null;

    #[ORM\Column(length: 150)]
    private ?string $descr = null;

    #[ORM\Column(length: 2000)]
    private ?string $systemPrompt = null;

    #[ORM\Column]
    private bool $isPublic = true;

    #[ORM\ManyToOne(inversedBy: 'agents')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false, onDelete: 'RESTRICT')]
    private ?User $user = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $logo = null;

    /**
     * @var Collection<int, AgentAccess>
     */
    #[ORM\OneToMany(targetEntity: AgentAccess::class, mappedBy: 'agent')]
    private Collection $agentAccesses;

    #[ORM\Column(type: Types::SMALLINT, options: ['unsigned' => true])]
    private ?int $maxTokens = null;

    #[ORM\Column]
    private ?float $temperature = null;

    #[ORM\Column]
    private ?float $topP = null;

    #[ORM\Column(type: Types::SMALLINT, options: ['unsigned' => true])]
    private ?int $topK = null;

    #[ORM\Column]
    private ?float $presencePenalty = null;

    #[ORM\Column]
    private ?float $frequencyPenalty = null;

    /**
     * @var Collection<int, Chat>
     */
    #[ORM\OneToMany(targetEntity: Chat::class, mappedBy: 'agent')]
    private Collection $chats;

    public function __construct()
    {
        $this->agentAccesses = new ArrayCollection();
        $this->chats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getDescr(): ?string
    {
        return $this->descr;
    }

    public function setDescr(string $descr): static
    {
        $this->descr = $descr;

        return $this;
    }

    public function getSystemPrompt(): ?string
    {
        return $this->systemPrompt;
    }

    public function setSystemPrompt(string $systemPrompt): static
    {
        $this->systemPrompt = $systemPrompt;

        return $this;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): static
    {
        $this->isPublic = $isPublic;

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

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collection<int, AgentAccess>
     */
    public function getAgentAccesses(): Collection
    {
        return $this->agentAccesses;
    }

    public function addAgentAccess(AgentAccess $agentAccess): static
    {
        if (!$this->agentAccesses->contains($agentAccess)) {
            $this->agentAccesses->add($agentAccess);
            $agentAccess->setAgent($this);
        }

        return $this;
    }

    public function removeAgentAccess(AgentAccess $agentAccess): static
    {
        if ($this->agentAccesses->removeElement($agentAccess)) {
            // set the owning side to null (unless already changed)
            if ($agentAccess->getAgent() === $this) {
                $agentAccess->setAgent(null);
            }
        }

        return $this;
    }

    public function getMaxTokens(): ?int
    {
        return $this->maxTokens;
    }

    public function setMaxTokens(int $maxTokens): static
    {
        $this->maxTokens = $maxTokens;

        return $this;
    }

    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function setTemperature(float $temperature): static
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getTopP(): ?float
    {
        return $this->topP;
    }

    public function setTopP(float $topP): static
    {
        $this->topP = $topP;

        return $this;
    }

    public function getTopK(): ?int
    {
        return $this->topK;
    }

    public function setTopK(int $topK): static
    {
        $this->topK = $topK;

        return $this;
    }

    public function getPresencePenalty(): ?float
    {
        return $this->presencePenalty;
    }

    public function setPresencePenalty(float $presencePenalty): static
    {
        $this->presencePenalty = $presencePenalty;

        return $this;
    }

    public function getFrequencyPenalty(): ?float
    {
        return $this->frequencyPenalty;
    }

    public function setFrequencyPenalty(float $frequencyPenalty): static
    {
        $this->frequencyPenalty = $frequencyPenalty;

        return $this;
    }

    /**
     * @return Collection<int, Chat>
     */
    public function getChats(): Collection
    {
        return $this->chats;
    }

    public function addChat(Chat $chat): static
    {
        if (!$this->chats->contains($chat)) {
            $this->chats->add($chat);
            $chat->setAgent($this);
        }

        return $this;
    }

    public function removeChat(Chat $chat): static
    {
        if ($this->chats->removeElement($chat)) {
            // set the owning side to null (unless already changed)
            if ($chat->getAgent() === $this) {
                $chat->setAgent(null);
            }
        }

        return $this;
    }
}
