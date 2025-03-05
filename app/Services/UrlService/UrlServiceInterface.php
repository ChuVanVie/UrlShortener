<?php

namespace App\Services\UrlService;

use App\Models\Url;

interface UrlServiceInterface
{
    public function getLongUrl(string $shortCode): ?Url;

    public function createShortUrl(string $longUrl): Url;

    public function incrementClickCount(Url $url);

    public function isSafeUrl(string $url): bool;
}
