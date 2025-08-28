<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class UniqueUserUsernameAndEmailValidator extends ConstraintValidator
{
    public function __construct(public readonly EntityManagerInterface $entityManager) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        $existingUser = $this->entityManager->getRepository(User::class)->findOneByUsernameOrEmail(
            username: $value->getUsername(),
            email: $value->getEmail(),
        );

        if (!empty($existingUser)) {
            if ($existingUser->getUsername() === $value->getUsername()) {
                $this->context->buildViolation($constraint->usernameMessage)
                    ->atPath('username')
                    ->addViolation();
            }
            if ($existingUser->getEmail() === $value->getEmail()) {
                $this->context->buildViolation($constraint->emailMessage)
                    ->atPath('email')
                    ->addViolation();
            }
        }
    }
}
