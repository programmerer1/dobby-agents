<?php

namespace App\Service;

use App\Dto\UserPasswordChangeDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordChangeService
{
    public function __construct(
        public readonly EntityManagerInterface $entityManager,
        public readonly UserPasswordHasherInterface $userPasswordHasher,
        public readonly UserService $userService
    ) {}

    public function changePassword(UserPasswordChangeDto $userPasswordChangeDto): JsonResponse
    {
        if (!$this->userPasswordHasher->isPasswordValid($this->userService->getUser(), $userPasswordChangeDto->currentPassword)) {
            return new JsonResponse(['status' => 401, 'error' => 'Incorrect current password']);
        }

        $this->userService->getUser()->setPassword(
            $this->userPasswordHasher->hashPassword($this->userService->getUser(), $userPasswordChangeDto->newPassword)
        );
        $this->entityManager->flush();
        return new JsonResponse(['status' => 200, 'message' => 'New password saved']);
    }
}
