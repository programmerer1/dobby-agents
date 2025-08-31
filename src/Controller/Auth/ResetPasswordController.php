<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Dto\CreateResetPasswordTokenDto;
use App\Service\CreateResetPasswordTokenService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
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
                'message' => 'You will now be redirected to your account. If your browser does not support redirects, refresh page.',
                'redirect' => $this->generateUrl('app_dashboard')
            ]);
        }

        return $createResetPasswordTokenService->createResetToken($createResetPasswordTokenDto);
    }

    #[Route(path: '/reset-password', name: 'app_auth_reset_password_token_page', methods: ['GET'])]
    public function resetPasswordPage() {}
}
