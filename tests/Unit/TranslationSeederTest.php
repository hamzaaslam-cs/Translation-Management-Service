<?php

use App\Models\Translation;
use Database\Seeders\TranslationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('creates 100002 translation records', function () {
    $this->seed(TranslationSeeder::class);
    expect(Translation::count())->toBe(100002);
});
