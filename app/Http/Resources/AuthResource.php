<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $token = $this->createToken('translation-management', ['*'], now()->addWeek())->plainTextToken;

        return array_merge([
            'name' => $this->name,
            'email' => $this->email,
            'token' => $token,
        ], $this->additional);
    }
}
