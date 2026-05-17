<?php

declare(strict_types=1);

use App\DTOs\PetData;
use App\Enums\PetStatus;

function expectedPetData(): PetData
{
    return new PetData(
        id: 1,
        name: 'Lilo',
        status: PetStatus::AVAILABLE,
        categoryName: 'Dogs',
        tags: ['friendly'],
        photoUrls: ['https://example.com/dog.jpg'],
    );
}

function petInput(): array
{
    return [
        'name' => 'Lilo',
        'status' => 'available',
        'category_name' => 'Dogs',
        'tags' => ['friendly'],
        'photo_urls' => ['https://example.com/dog.jpg'],
    ];
}
