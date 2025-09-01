<?php

namespace App\Security;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{Request, JsonResponse};
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class LoginFailureHandler implements AuthenticationFailureHandlerInterface
{
    public function __construct(
        public readonly EntityManagerInterface $entityManager,
        public readonly UserRepository $userRepository
    ) {}

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if ($email) {
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if ($user) {
                $user->incrementFailedAttempts();
                $this->entityManager->flush();
            }
        }

        return new JsonResponse(['status' => 401, 'error' => 'Invalid credentials'], 401);
    }
}
