<?php

namespace App\Models;

use App\Jobs\UpdateTranslationCache;
use App\Traits\HasFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory, HasFilters;

    protected $fillable = [
        'locale',
        'key',
        'content',
        'tags',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            self::updateJsonFile();
        });

        static::deleted(function () {
            self::updateJsonFile();
        });
    }

    private static function updateJsonFile()
    {
        UpdateTranslationCache::dispatch();
    }
}
