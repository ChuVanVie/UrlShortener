<?php

namespace App\Repositories\UrlRepository;

use App\Models\Url;
use App\Repositories\BaseRepository;

class UrlRepository extends BaseRepository implements UrlRepositoryInterface
{
    public function __construct(Url $model)
    {
        parent::__construct($model);
    }

    public function findByShortCodeHash(string $shortCodeHash): ?Url
    {
        return $this->model
            ->where('short_code_hash', $shortCodeHash)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();
    }

    public function existsByShortCodeHash(string $shortCodeHash): bool
    {
        return $this->model->where('short_code_hash', $shortCodeHash)->exists();
    }

    public function incrementClicks(string $shortCodeHash)
    {
        return $this->model
            ->where('short_code_hash', $shortCodeHash)
            ->increment('clicks');
    }
}
