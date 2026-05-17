<?php

declare(strict_types=1);

use App\Http\Controllers\PetController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/pets');

Route::middleware('throttle:60,1')->group(function () {
    Route::resource('pets', PetController::class)
        ->parameters(['pets' => 'id'])
        ->whereNumber('id');
});
