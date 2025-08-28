<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface|User $user): void
    {
        if (method_exists($user, 'isBanned') && $user->isBanned()) {
            throw new CustomUserMessageAuthenticationException('Your account is banned.');
        }

        if (method_exists($user, 'isVerified') && !$user->isVerified()) {
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        /* Optional post-authentication checks */
    }
}
