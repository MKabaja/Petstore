<?php

declare(strict_types=1);

namespace Tests;

use App\Services\PetService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery\MockInterface;

abstract class TestCase extends BaseTestCase
{
    public MockInterface&PetService $service;
}
