<?php

namespace Database\Factories;

use App\Models\Url;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UrlFactory extends Factory
{
    protected $model = Url::class;

    public function definition(): array
    {
        do {
            $shortCode = Str::random(8);
            $shortCodeHash = hash_hmac('sha256', $shortCode, env('APP_KEY'));
        } while (\App\Models\Url::where('short_code_hash', $shortCodeHash)->exists());
    
        return [
            'short_code' => $shortCode,
            'short_code_hash' => $shortCodeHash,
            'long_url' => $this->faker->unique()->url(),
            'clicks' => 0,
            'expires_at' => now()->addDays(7),
        ];
    }
}
