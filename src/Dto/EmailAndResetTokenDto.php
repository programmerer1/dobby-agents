<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class EmailAndResetTokenDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        #[Assert\Length(max: 120)]
        public readonly string $email,

        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        public readonly string $token,
    ) {}
}
