<?php

namespace Database\Factories;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

// Ensure this points to your Translation model

/**
 * @extends Factory<Translation>
 */
class TranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Translation::class;

    public function definition(): array
    {
        return [
            'locale' => $this->faker->languageCode,
            'key' => $this->faker->word,
            'content' => $this->faker->sentence,
            'tags' => json_encode([$this->faker->word]),
        ];
    }
}
