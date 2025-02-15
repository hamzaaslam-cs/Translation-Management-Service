<?php

namespace App\Jobs;

use App\Models\Translation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class UpdateTranslationCache implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->updateCache();
    }

    public function updateCache(): void
    {
        $disk = config('filesystems.default'); // Get the default storage disk

        if (Cache::has('translations_json_file')) {
            $oldFilePath = Cache::get('translations_json_file');

            // Delete the old file from the appropriate disk
            Storage::disk($disk)->delete($oldFilePath);
        }

        $fileName = 'translations_' . now()->timestamp . '.json.gz';
        $filePath = "exports/$fileName";

        // Stream the JSON content and compress
        Storage::disk($disk)->put($filePath, gzencode($this->generateJson()));

        // Store the new file path in cache
        Cache::forever('translations_json_file', $filePath);
    }

    private function generateJson(): string
    {
        return '[' . Translation::select(['id', 'locale', 'key', 'content', 'tags'])
                ->lazy()
                ->map(fn($t) => json_encode([
                    'id' => $t->id,
                    'locale' => $t->locale,
                    'key' => $t->key,
                    'content' => $t->content,
                    'tags' => $t->tags,
                ]))
                ->implode(',') . ']';
    }
}
