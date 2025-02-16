<?php

namespace Database\Seeders;

use App\Jobs\UpdateTranslationCache;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $locales = ['en', 'fr', 'es']; // Example locales
        $tags = ['mobile', 'desktop', 'web']; // Example tags
        $batchSize = 1000; // Number of records per batch
        $records = []; // Store records before batch insert

        foreach ($locales as $locale) {
            $usedKeys = []; // Track used keys for the current locale

            for ($i = 0; $i < 33334; $i++) {
                do {
                    $key = $faker->word().'_'.$faker->numberBetween(1000, 999999);
                } while (isset($usedKeys[$key])); // Ensure key is unique for the locale

                $usedKeys[$key] = true; // Mark key as used

                $records[] = [
                    'locale' => $locale,
                    'key' => $key,
                    'content' => $faker->sentence(),
                    'tags' => json_encode($faker->randomElements($tags, rand(1, 3))),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Batch insert every $batchSize records
                if (count($records) >= $batchSize) {
                    DB::table('translations')->insert($records);
                    $records = []; // Reset batch
                }
            }
        }

        // Insert remaining records
        if (! empty($records)) {
            DB::table('translations')->insert($records);
        }

        UpdateTranslationCache::dispatchSync();
    }
}
