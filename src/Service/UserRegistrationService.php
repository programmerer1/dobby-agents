<?php

namespace App\Service;

use App\Dto\RegisterUserDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserRegistrationService
{
    public function __construct(
        public readonly EntityManagerInterface $entityManager,
        public readonly ValidatorInterface $validator,
        public readonly User $user,
        public readonly UserPasswordHasherInterface $passwordHasher,
        public readonly UrlGeneratorInterface $UrlGeneratorInterface,
    ) {}

    public function registerUser(RegisterUserDto $registerUserDto): JsonResponse
    {
        $this->user->setEmail($registerUserDto->email)
            ->setUsername($registerUserDto->username)
            ->setPassword($this->passwordHasher->hashPassword($this->user, $registerUserDto->password));
        $errors = $this->validator->validate($this->user);
        $response = [];

        if (count($errors) > 0) {
            $response['status'] = 422;

            foreach ($errors as $error) {
                $response['errors'][] = [
                    'path' => $error->getPropertyPath(),
                    'message' =>  $error->getMessage()
                ];
            }
        } else {
            $this->entityManager->persist($this->user);
            $this->entityManager->flush();
            $response = [
                'status' => 200,
                'message' => 'You have registered. You will now be redirected to the authentication page. If your browser does not support redirects, refresh page.',
                'redirect' => $this->UrlGeneratorInterface->generate('app_auth_login')
            ];
        }

        return new JsonResponse($response);
    }
}
