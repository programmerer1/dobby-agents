<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\{Chat, User};

final class ChatVoter extends Voter
{
    public const VIEW = 'chat_view';
    public const MESSAGE_SEND = 'chat_message_send';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::VIEW, self::MESSAGE_SEND])
            && $subject instanceof Chat;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::VIEW => $this->canView($subject, $user),
            self::MESSAGE_SEND => $this->canNewMessageSend($subject, $user),
            default => false,
        };
    }

    private function canView(Chat $chat, User $user): bool
    {
        return $chat->getUser() === $user;
    }

    private function canNewMessageSend(Chat $chat, User $user): bool
    {
        return $chat->getUser() === $user;
    }
}
