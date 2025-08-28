<?php

namespace App\Dto;

final class UserDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly string $username,
    ) {}
}
