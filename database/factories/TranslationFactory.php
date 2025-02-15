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
        static $usedKeys = [];

        do {
            $key = $this->faker->word().'_'.$this->faker->unique()->numberBetween(1000, 9999);
        } while (in_array($key, $usedKeys));

        $usedKeys[] = $key;

        return [
            'locale' => $this->faker->randomElement(['en', 'fr', 'es']),
            'key' => $key,
            'value' => $this->faker->sentence(),
            'tags' => json_encode($this->faker->randomElements(['mobile', 'desktop', 'web'], rand(1, 3))),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];
    }
}
