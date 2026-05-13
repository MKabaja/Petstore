<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\PetStoreError;
use RuntimeException;

abstract class PetStoreException extends RuntimeException
{
    public function __construct(PetStoreError $error)
    {
        parent::__construct($error->value);
    }
}
