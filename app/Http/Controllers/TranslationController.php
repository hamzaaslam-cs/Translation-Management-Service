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
use Illuminate\Support\Facades\Storage;

/**

 *
 * @OA\Server(
 *     url="/api",
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="header",
 *     securityScheme="Bearer",
 *     name="Authorization",
 *     description="Enter token in format (Bearer <token>)"
 * )
 */
class TranslationController extends Controller
{
    public function __construct(public TranslationRepository $translationRepository)
    {

    }

    /**
     * @OA\Get(
     *     path="/translations",
     *     summary="Get all translations",
     *     tags={"Translations"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of translations",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="key", type="string"),
     *                 @OA\Property(property="value", type="string"),
     *                 @OA\Property(property="tags", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index(TranslationFilters $filters)
    {
        $translations = $this->translationRepository->all($filters);
        return response()->json($translations);
    }

    /**
     * @OA\Get(
     *     path="/translations/{id}",
     *     summary="Get specific translation",
     *     tags={"Translations"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation details",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="key", type="string"),
     *             @OA\Property(property="value", type="string"),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Translation not found"
     *     )
     * )
     */
    public function show(Request $request, $id): JsonResponse
    {
        $translation = $this->translationRepository->find($id);
        return response()->json($translation);
    }

    /**
     * @OA\Post(
     *     path="/translations",
     *     summary="Create new translation",
     *     tags={"Translations"},
     *     security={{"Bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"key","value"},
     *             @OA\Property(property="key", type="string", example="welcome_message"),
     *             @OA\Property(property="value", type="string", example="Welcome to our application"),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string"), example={"general", "welcome"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation created successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors"
     *     )
     * )
     */
    public function store(StoreTranslationRequest $request)
    {
        $data = $request->validated();
        $data['tags'] = json_encode($data['tags'] ?? []);
        $translation = $this->translationRepository->store($data);
        return response()->json($translation);
    }

    /**
     * @OA\Put(
     *     path="/translations/{id}",
     *     summary="Update translation",
     *     tags={"Translations"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="key", type="string"),
     *             @OA\Property(property="value", type="string"),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation updated successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Translation not found"
     *     )
     * )
     */
    public function update(UpdateTranslationRequest $request, $id)
    {
        $data = $request->validated();
        if (isset($data['tags'])) {
            $data['tags'] = json_encode($data['tags']);
        }
        $translation = $this->translationRepository->update($data, $id);
        return response()->json($translation);
    }

    /**
     * @OA\Delete(
     *     path="/translations/{id}",
     *     summary="Delete translation",
     *     tags={"Translations"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Translation not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $this->translationRepository->destroy($id);
        return response()->json(["message" => trans("general.deleted")]);
    }

    /**
     * @OA\Get(
     *     path="/translations/export",
     *     summary="Export translations as JSON",
     *     tags={"Translations"},
     *     security={{"Bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="JSON file URL",
     *         @OA\JsonContent(
     *             @OA\Property(property="file_url", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function exportJson(): JsonResponse
    {
        if (!Cache::has('translations_json_file')) {
            UpdateTranslationCache::dispatchSync();
        }

        return response()->json([
            'file_url' => url(Storage::url(Cache::get('translations_json_file'))),
        ]);
    }
}
