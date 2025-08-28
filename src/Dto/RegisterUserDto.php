<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class RegisterUserDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        #[Assert\Length(max: 120)]
        public readonly string $email,

        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 100)]
        public readonly string $username,

        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 50)]
        public readonly string $password,

        #[Assert\NotBlank]
        #[Assert\Length(max: 50)]
        #[Assert\EqualTo(propertyPath: "password", message: "Passwords do not match")]
        public readonly string $password_confirmation,

        #[Assert\NotBlank]
        public readonly mixed $agree,
    ) {}
}
