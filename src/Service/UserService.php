<?php

namespace App\Service;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\User;

class UserService
{
    public function __construct(public readonly TokenStorageInterface $tokenStorageInterface) {}

    /** The VSC IDE did not recognize the methods, so I had to create this method.  */
    public function getUser(): User
    {
        return $this->tokenStorageInterface->getToken()->getUser();
    }
}
