<?php

namespace Database\Factories;

use App\Models\PasswordReset;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends Factory<PasswordReset>
 */
class PasswordResetFactory extends Factory
{
    protected $model = PasswordReset::class;

    public function definition()
    {
        return [
            'email' => $this->faker->safeEmail,
            'token' => bcrypt(Str::random(64)),
            'expires_at' => Carbon::now()->addMinutes(3),
        ];
    }
}
