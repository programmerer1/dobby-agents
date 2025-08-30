<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateFireworksApiKeyDto
{
    public function __construct(
        #[Assert\Length(max: 255)]
        public readonly ?string $apiKey,
    ) {}
}
