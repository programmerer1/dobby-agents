<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class UniqueUserUsernameAndEmail extends Constraint
{
    public string $usernameMessage = 'The entered username is already taken.';
    public string $emailMessage = 'The entered email is already taken.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
