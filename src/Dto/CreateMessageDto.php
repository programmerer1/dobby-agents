<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateMessageDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 5000)]
        public readonly string $text,
    ) {}
}
