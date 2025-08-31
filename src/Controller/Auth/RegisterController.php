<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Response};
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use App\Dto\RegisterUserDto;
use App\Service\UserRegistrationService;

final class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_auth_register', methods: ['GET'])]
    public function index(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('auth/register.html.twig', [
            'controller_name' => 'Auth/RegisterController',
        ]);
    }

    #[Route('/register', name: 'app_auth_register_store', methods: ['POST'], format: 'json')]
    public function register(
        #[MapRequestPayload()] RegisterUserDto $registerUserDto,
        UserRegistrationService $userRegistrationService
    ): JsonResponse {
        if ($this->getUser()) {
            return $this->json([
                'status' => 200,
                'message' => 'Login successful. You will now be redirected to your account. If your browser does not support redirects, refresh page.',
                'redirect' => $this->generateUrl('app_dashboard')
            ]);
        }

        return $userRegistrationService->registerUser($registerUserDto);
    }
}
