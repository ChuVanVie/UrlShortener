<?php

namespace App\Repositories\UrlRepository;

use App\Models\Url;
use App\Repositories\BaseRepositoryInterface;

interface UrlRepositoryInterface extends BaseRepositoryInterface
{
    public function findByShortCodeHash(string $shortCodeHasg): ?Url;

    public function existsByShortCodeHash(string $shortCodeHash): bool;

    public function incrementClicks(string $shortCodeHash);
}
