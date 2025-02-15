<?php

namespace App\Repositories;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthRepository
{
    public function store(array $attributes)
    {
        return DB::transaction(function () use ($attributes) {
            return User::create([
                'name' => $attributes['name'],
                'email' => $attributes['email'],
                'password' => bcrypt($attributes['password']),
            ]);
        });
    }

    public function update($id, array $attributes)
    {
        return User::where('id', $id)->update($attributes);
    }

    public function forgetPassword($email): ?string
    {
        if (! PasswordReset::where('email', '=', $email)->where('expires_at', '<', Carbon::now())->exists()) {
            PasswordReset::where('email', '=', $email)->delete();
            $token = Str::random(64);
            PasswordReset::create(['email' => $email, 'token' => bcrypt($token), 'expires_at' => Carbon::now()->addMinutes(3)]);

            return $token;
        } else {
            return null;
        }
    }
}
