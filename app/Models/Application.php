<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Application extends Model
{

    protected $fillable = [
        'name',
        'description',
        'pic_name',
        'status',
        'secret_key',
    ];

    public static function generateSecretKey(): string
    {
        return Str::random(56);
    }
}