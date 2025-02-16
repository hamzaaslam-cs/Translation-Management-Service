<?php

declare(strict_types=1);

use App\Filters\QueryFilterBase;
use App\Models\Translation;
use App\Repositories\TranslationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->repository = new TranslationRepository;
});

describe('TranslationRepository Unit Tests', function () {

    it('retrieves all translations', function () {
        Translation::factory()->count(5)->create();

        $translations = $this->repository->all();

        expect($translations)->toHaveCount(5);
    });

    it('retrieves a translation by id', function () {
        $translation = Translation::factory()->create();

        $foundTranslation = $this->repository->find($translation->id);

        expect($foundTranslation->id)->toBe($translation->id);
    });

    it('stores a new translation', function () {
        $data = Translation::factory()->make()->toArray();

        $translation = $this->repository->store($data);

        expect($translation)->toBeInstanceOf(Translation::class)
            ->and($translation->exists)->toBeTrue();
    });

    it('updates a translation', function () {
        $translation = Translation::factory()->create();
        $newData = ['content' => 'Updated translation text'];

        $updatedTranslation = $this->repository->update($newData, $translation->id);

        expect($updatedTranslation->content)->toBe('Updated translation text');
    });

    it('deletes a translation', function () {
        $translation = Translation::factory()->create();

        $deleted = $this->repository->destroy($translation->id);

        expect($deleted)->toBe(1)
            ->and(Translation::find($translation->id))->toBeNull();
    });

    it('applies filters when retrieving all translations', function () {
        // Create mock request with filter parameters
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive()->andReturn(['locale' => 'en']);

        // Mock QueryFilterBase with request
        $filterMock = Mockery::mock(QueryFilterBase::class, [$requestMock]);
        $filterMock->shouldReceive('hasFilters')->andReturn(true);
        $filterMock->shouldReceive('apply')->andReturnUsing(function ($query) {
            return $query->where('locale', 'en');
        });

        // Create translations with different languages
        Translation::factory()->create(['locale' => 'en']);
        Translation::factory()->create(['locale' => 'fr']);

        // Run repository method with filter
        $translations = $this->repository->all($filterMock);

        // Assert only English translations are returned
        expect($translations->count())->toBe(1)
            ->and($translations->first()->locale)->toBe('en');
    });
});
