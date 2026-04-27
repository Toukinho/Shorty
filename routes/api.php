<?php

use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/url', UrlController::class);
