<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\PetStatus;

final class PetData
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly PetStatus $status,
        public readonly ?string $categoryName,
        /** @var string[] */
        public readonly array $tags = [],
        /** @var string[] */
        public readonly array $photoUrls = []
    ) {}
}
