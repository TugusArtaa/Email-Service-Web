<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Application extends Model
{
    protected $fillable = [
        'name',
        'description',
        'password',
        'secret_key',
    ];

    protected $hidden = [
        'password',
    ];

    public static function generateSecretKey(): string
    {
        return Str::random(24);
    }
}