<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; 

class Url extends Model
{
    use HasFactory;

    protected $fillable = ['url', 'short_url'];

    protected static function booted(): void
    {
        static::creating(function (Url $url) {
            $url->short_url = self::generateUniqueShortUrl();
        });
    }

    private static function generateUniqueShortUrl(): string
    {
        do {
            $code = Str::random(6);
        } while (self::where('short_url', $code)->exists());

        return $code;
    }
}