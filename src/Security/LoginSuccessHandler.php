<?php

namespace App\Security;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\{Request, JsonResponse};
use App\Entity\User;
use App\Service\LoginFailedAttemptsService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        public readonly LoginFailedAttemptsService $loginFailedAttemptsService,
        public readonly UrlGeneratorInterface $urlGenerator
    ) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $user = $token->getUser();

        if ($user instanceof User) {
            $this->loginFailedAttemptsService->reset($user);
        }

        return new JsonResponse([
            'status' => 200,
            'message' => 'Login successful. You will now be redirected to your account.',
            'redirect' => $this->urlGenerator->generate('app_dashboard')
        ]);
    }
}
