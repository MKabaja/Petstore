<?php

declare(strict_types=1);

namespace App\Enums;

enum PetStoreError: string
{
    case NOT_FOUND = 'Pet not found.';
    case UNAVAILABLE = 'Service unavailable. Please try again later.';
    case INVALID_DATA = 'Invalid data provided.';
    case CLIENT_ERROR = 'Invalid API request.';
    // case INVALID_RESPONSE = 'Invalid response from API.';

}
