<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\User;

final class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_auth_login', methods: ['GET'])]
    public function index(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('auth/login.html.twig', [
            'controller_name' => 'Auth/LoginController',
        ]);
    }

    #[Route('/login', name: 'app_json_login', methods: ['POST'], format: 'json')]
    public function login(#[CurrentUser] ?User $user): JsonResponse
    {
        if (null === $user) {
            return new JsonResponse(['status' => 401, 'error' => 'Invalid credentials']);
        }

        return new JsonResponse([
            'status' => 200,
            'message' => 'Login successful. You will now be redirected to your account. If your browser does not support redirects, refresh page.',
            'redirect' => $this->generateUrl('app_dashboard')
        ]);
    }
}
