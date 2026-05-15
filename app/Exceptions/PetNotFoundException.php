<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\PetStoreError;

final class PetNotFoundException extends PetStoreException
{
    public function __construct()
    {
        parent::__construct(PetStoreError::NOT_FOUND);
    }
}
