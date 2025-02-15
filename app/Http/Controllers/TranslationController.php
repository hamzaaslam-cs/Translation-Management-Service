<?php

namespace App\Http\Controllers;

use App\Filters\TranslationFilters;
use App\Http\Requests\Translation\StoreTranslationRequest;
use App\Http\Requests\Translation\UpdateTranslationRequest;
use App\Jobs\UpdateTranslationCache;
use App\Repositories\TranslationRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

class TranslationController extends Controller
{
    public function __construct(public TranslationRepository $translationRepository)
    {
    }

    public function index(TranslationFilters $filters)
    {
        $translations = $this->translationRepository->all($filters);

        return response()->json($translations);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $translation = $this->translationRepository->find($id);
        return response()->json($translation);
    }

    public function store(StoreTranslationRequest $request)
    {
        $data = $request->validated();

        $data['tags'] = json_encode($data['tags'] ?? []);

        $translation = $this->translationRepository->store($data);

        return response()->json($translation);
    }

    public function update(UpdateTranslationRequest $request, $id)
    {
        $data = $request->validated();

        if (isset($data['tags'])) {
            $data['tags'] = json_encode($data['tags']);
        }
        $translation = $this->translationRepository->update($data, $id);

        return response()->json($translation);
    }

    public function destroy($id)
    {
        $this->translationRepository->destroy($id);

        return response()->json(["message" => trans("general.deleted")]);
    }

    public function exportJson(): JsonResponse
    {
        if (!Cache::has('translations_json_file')) {
            UpdateTranslationCache::dispatchSync();
        }

        return response()->json([
            'file_url' => asset("storage/exports/" . basename(Cache::get('translations_json_file'))),
        ]);
    }
}
