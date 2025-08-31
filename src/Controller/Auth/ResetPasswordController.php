<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Dto\{CreateResetPasswordTokenDto, EmailAndResetTokenDto, UserResetPasswordDto};
use App\Repository\UserPasswordResetTokenRepository;
use App\Service\{CreateResetPasswordTokenService, ResetPasswordService};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\HttpKernel\Attribute\{MapQueryString, MapRequestPayload};
use Symfony\Component\Routing\Attribute\Route;

final class ResetPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'app_auth_forgot_password', methods: ['GET'])]
    public function index(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('auth/forgot-password.html.twig');
    }

    #[Route('/forgot-password', name: 'app_auth_reset_password_token_create', methods: ['POST'], format: 'json')]
    public function createResetToken(
        #[MapRequestPayload()] CreateResetPasswordTokenDto $createResetPasswordTokenDto,
        CreateResetPasswordTokenService $createResetPasswordTokenService
    ) {
        if ($this->getUser()) {
            return $this->json([
                'status' => 200,
                'message' => 'You will now be redirected to your account.',
                'redirect' => $this->generateUrl('app_dashboard')
            ]);
        }

        return $createResetPasswordTokenService->createResetToken($createResetPasswordTokenDto);
    }

    #[Route(path: '/reset-password', name: 'app_auth_reset_password_token_page', methods: ['GET'])]
    public function resetPasswordPage(
        #[MapQueryString()] EmailAndResetTokenDto $emailAndResetTokenDto,
        UserPasswordResetTokenRepository $userPasswordResetTokenRepository
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        $tokenEntity = $userPasswordResetTokenRepository->findValidToken($emailAndResetTokenDto->token);

        if (empty($tokenEntity) || $tokenEntity->getUser()->getEmail() !== $emailAndResetTokenDto->email) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('auth/reset-password.html.twig', [
            'email' => $emailAndResetTokenDto->email,
            'token' => $emailAndResetTokenDto->token,
        ]);
    }

    #[Route(path: '/reset-password', name: 'app_auth_reset_password', methods: ['POST'], format: 'json')]
    public function resetPassword(
        #[MapRequestPayload()] EmailAndResetTokenDto $emailAndResetTokenDto,
        #[MapRequestPayload()] UserResetPasswordDto $userResetPasswordDto,
        ResetPasswordService $resetPasswordService
    ): JsonResponse {
        if ($this->getUser()) {
            return $this->json([
                'status' => 200,
                'message' => 'You will now be redirected to your account.',
                'redirect' => $this->generateUrl('app_dashboard')
            ]);
        }

        return $resetPasswordService->reset($emailAndResetTokenDto, $userResetPasswordDto);
    }
}
