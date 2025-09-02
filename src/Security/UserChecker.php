<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\{UserCheckerInterface, UserInterface};
use App\Entity\User;
use App\Service\LoginFailedAttemptsService;

class UserChecker implements UserCheckerInterface
{
    public function __construct(public readonly LoginFailedAttemptsService $loginFailedAttemptsService) {}

    public function checkPreAuth(UserInterface|User $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->isBanned()) {
            throw new CustomUserMessageAccountStatusException('Your account is banned.');
        }

        if (!$user->isVerified()) {
        }

        if ($this->loginFailedAttemptsService->isLocked($user)) {
            throw new CustomUserMessageAccountStatusException(
                'Account temporarily locked due to too many login attempts.'
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        /* Optional post-authentication checks */
    }
}
