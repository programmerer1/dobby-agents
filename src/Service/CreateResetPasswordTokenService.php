<?php

namespace App\Service;

use App\Dto\CreateResetPasswordTokenDto;
use App\Entity\UserPasswordResetToken;
use App\Repository\{UserPasswordResetTokenRepository, UserRepository};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use \DateTimeImmutable;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CreateResetPasswordTokenService
{
    public function __construct(
        public readonly EntityManagerInterface $entityManager,
        public readonly UserRepository $userRepository,
        public readonly UserPasswordResetTokenRepository $userPasswordResetTokenRepository,
        public readonly EmailResetPasswordTokenService $emailResetPasswordTokenService,
        public readonly UrlGeneratorInterface $urlGeneratorInterface
    ) {}

    public function createResetToken(CreateResetPasswordTokenDto $createResetPasswordTokenDto): JsonResponse
    {
        $user = $this->userRepository->findOneBy([
            'email' => $createResetPasswordTokenDto->email,
            'isBanned' => 0
        ]);

        $response = [
            'status' => 200,
            'message' => 'We will send you a link to reset your password if the email address you provided is found in our system.'
        ];

        if (empty($user)) {
            return new JsonResponse($response);
        }

        $lastToken = $this->userPasswordResetTokenRepository->findOneBy(['user' => $user], ['expiresAt' => 'DESC']);

        if (!empty($lastToken) && $lastToken->getCreatedAt() > new DateTimeImmutable('-15 minutes')) {
            return new JsonResponse($response);
        }

        $passwordResetToken = new UserPasswordResetToken;
        $passwordResetToken->setUser($user);

        $resetUrl = $this->urlGeneratorInterface->generate('app_auth_reset_password_token_page', [
            'email' => $user->getEmail(),
            'token' => $passwordResetToken->getToken(),
        ]);
        $this->emailResetPasswordTokenService->send($resetUrl, $user->getEmail());

        $this->entityManager->persist($passwordResetToken);
        $this->entityManager->flush();

        return new JsonResponse($response);
    }
}
