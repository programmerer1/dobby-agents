<?php

namespace App\Security;

use App\Repository\UserRepository;
use App\Service\LoginFailedAttemptsService;
use Symfony\Component\HttpFoundation\{Request, JsonResponse};
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class LoginFailureHandler implements AuthenticationFailureHandlerInterface
{
    public function __construct(
        public readonly LoginFailedAttemptsService $loginFailedAttemptsService,
        public readonly UserRepository $userRepository
    ) {}

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $content = $request->getContent();
        $data = [];

        if (!empty($content)) {
            $decoded = json_decode($content, true);

            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        $email = $data['email'] ?? null;

        if ($email) {
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if ($user) {
                $this->loginFailedAttemptsService->increment($user);
            }
        }

        return new JsonResponse(['status' => 401, 'error' => 'Invalid credentials'], 401);
    }
}
