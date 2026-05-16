<?php

declare(strict_types=1);

use App\Exceptions\PetStoreApiException;
use App\Exceptions\PetStoreUnavailableException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (PetStoreUnavailableException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 503);
            }

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        });
        $exceptions->render(function (PetStoreApiException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        });
    })->create();
