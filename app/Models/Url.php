<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Encryption\Encrypter;

class Url extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'long_url',
        'short_code',
        'short_code_hash',
        'clicks',
        'expires_at',
    ];

    public function setLongUrlAttribute($value)
    {
        $this->attributes['long_url'] = encrypt($value);
    }

    public function getLongUrlAttribute($value)
    {
        return decrypt($value);
    }

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::creating(function ($url) {
    //         $url->short_code_hash = hash_hmac('sha256', $url->short_code, config('app.key'));
    //     });
    // }
}
