<?php

use App\Models\Url;
use Illuminate\Support\Facades\Route;

Route::get('/{code}', function (string $code) {
    $url = Url::where('short_url', $code)->firstOrFail();
    $url->increment('access_count');
    return redirect($url->url, 301);
});