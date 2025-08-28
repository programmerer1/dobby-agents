<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateAgentDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 100)]
        public readonly string $name,

        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 150)]
        public readonly string $descr,

        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 2000)]
        public readonly string $systemPrompt,

        public readonly int|bool $isPublic,

        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        #[Assert\Range(min: 1, max: 16384)]
        public readonly int $maxTokens,

        #[Assert\NotBlank]
        #[Assert\Type(['float', 'integer'])]
        #[Assert\Range(min: 0.0, max: 2.0)]
        public readonly int|float $temperature,

        #[Assert\NotBlank]
        #[Assert\Type(['float', 'integer'])]
        #[Assert\Range(min: 0.0, max: 1.0)]
        public readonly int|float $topP,

        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        #[Assert\Range(min: 1, max: 100)]
        public readonly int $topK,

        #[Assert\NotBlank]
        #[Assert\Type(['float', 'integer'])]
        #[Assert\Range(min: -2.0, max: 2.0)]
        public readonly int|float $presencePenalty,

        #[Assert\NotBlank]
        #[Assert\Type(['float', 'integer'])]
        #[Assert\Range(min: -2.0, max: 2.0)]
        public readonly int|float $frequencyPenalty,
    ) {}
}
