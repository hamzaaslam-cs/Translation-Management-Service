<?php

use App\Jobs\UpdateTranslationCache;
use App\Models\Translation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake(config('filesystems.default'));
    Cache::flush(); // Clear cache

});

it('stores a new translations file and caches the file path', function () {
    // Create test translations in the database
    Translation::factory()->count(5)->create();
    // Dispatch the job

    $job = new UpdateTranslationCache();
    $job->handle();

    // Assert a file is stored in 's3'
    $storedFiles = Storage::allFiles('exports');

    expect($storedFiles)->toHaveCount(1);

    // Assert the cache contains the file path
    $cachedFilePath = Cache::get('translations_json_file');
    expect($cachedFilePath)->not->toBeNull();

    // Verify the file exists in storage
    Storage::assertExists($cachedFilePath);
});

it('deletes the old file if it exists in cache', function () {
    // Fake an old file in storage
    $oldFilePath = 'exports/old_translations.json.gz';
    Storage::put($oldFilePath, 'old data');
    Cache::forever('translations_json_file', $oldFilePath);

    // Dispatch the job
    $job = new UpdateTranslationCache();
    $job->handle();

    // Ensure old file is deleted
    Storage::assertMissing($oldFilePath);

    // Ensure a new file is created and cached
    $newFilePath = Cache::get('translations_json_file');
    expect($newFilePath)->not->toBe($oldFilePath);
    Storage::assertExists($newFilePath);
});
