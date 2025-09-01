<?php

namespace App\Service;

use App\Dto\{EmailAndResetTokenDto, UserResetPasswordDto};
use App\Repository\{UserPasswordResetTokenRepository};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordService
{
    public function __construct(
        public readonly EntityManagerInterface $entityManager,
        public readonly UserPasswordResetTokenRepository $userPasswordResetTokenRepository,
        public readonly UrlGeneratorInterface $urlGeneratorInterface,
        public readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function reset(
        EmailAndResetTokenDto $emailAndResetTokenDto,
        UserResetPasswordDto $userResetPasswordDto
    ): JsonResponse {
        $tokenEntity = $this->userPasswordResetTokenRepository->findValidToken($emailAndResetTokenDto->token);

        if (empty($tokenEntity) || $tokenEntity->getUser()->getEmail() !== $emailAndResetTokenDto->email) {
            return new JsonResponse([
                'status' => 200,
                'message' => 'The link is outdated. Get a new link to reset your password.',
                'redirect' => $this->urlGeneratorInterface->generate('app_home')
            ]);
        }

        $user = $tokenEntity->getUser();

        $this->entityManager->wrapInTransaction(function () use ($user, $userResetPasswordDto) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $userResetPasswordDto->password));
            $this->userPasswordResetTokenRepository->deleteUserTokens($user);
        });


        return new JsonResponse([
            'status' => 200,
            'message' => 'Your password has been changed. You will now be redirected to the authentication page.',
            'redirect' => $this->urlGeneratorInterface->generate('app_auth_login')
        ]);
    }
}
