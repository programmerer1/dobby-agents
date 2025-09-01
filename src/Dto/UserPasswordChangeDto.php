<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class UserPasswordChangeDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 50)]
        public readonly string $currentPassword,

        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 50)]
        public readonly string $newPassword,

        #[Assert\NotBlank]
        #[Assert\Length(max: 50)]
        #[Assert\EqualTo(propertyPath: "newPassword", message: "Passwords do not match")]
        public readonly string $password_confirmation,
    ) {}
}
