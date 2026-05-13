<?php

declare(strict_types=1);

namespace App\Enums;

enum PetStatus: string
{
    case AVAILABLE = 'available';
    case PENDING = 'pending';
    case SOLD = 'sold';
    case UNKNOWN = 'unknown'; // fallback value for invalid input
}
