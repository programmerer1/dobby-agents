<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\{Agent, User};
use App\Repository\AgentRepository;

final class AgentVoter extends Voter
{
    public const EDIT = 'agent_edit';
    public const VIEW = 'agent_view';
    public const CHAT = 'agent_chat';
    public function __construct(public readonly AgentRepository $agentRepository) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::CHAT])
            && $subject instanceof Agent;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::VIEW => $this->canView($subject, $user),
            self::EDIT => $this->canEdit($subject, $user),
            self::CHAT => $this->canChat($subject, $user),
            default => false,
        };
    }

    private function canView(Agent $agent, User $user): bool
    {
        return $agent->getUser() === $user;
    }

    private function canEdit(Agent $agent, User $user): bool
    {
        return $agent->getUser() === $user;
    }

    private function canChat(Agent $agent, User $user): bool
    {
        return $this->agentRepository->userHasAccess($user, $agent);
    }
}
