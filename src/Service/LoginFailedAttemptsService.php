<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;

class LoginFailedAttemptsService
{
    private int $maxAttempts = 3;
    private int $lockMinutes = 15;

    public function __construct(public readonly EntityManagerInterface $entityManager) {}

    public function increment(User $user): void
    {
        $user->incrementLoginFailedAttempts();
        $this->entityManager->flush();
    }

    public function reset(User $user): void
    {
        $user->resetLoginFailedAttempts();
        $this->entityManager->flush();
    }

    public function isLocked(User $user): bool
    {
        if ($user->getLoginFailedAttempts() < $this->maxAttempts) {
            return false;
        }

        $lastFail = $user->getLastLoginFailedAt();
        if ($lastFail === null) {
            return false;
        }

        $unlockTime = $lastFail->modify("+{$this->lockMinutes} minutes");

        if (new DateTimeImmutable() < $unlockTime) {
            return true;
        }

        $this->reset($user);
        return false;
    }
}
