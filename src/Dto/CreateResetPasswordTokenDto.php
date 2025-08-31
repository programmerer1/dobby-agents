<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateResetPasswordTokenDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        #[Assert\Length(max: 120)]
        public readonly string $email,
    ) {}
}
