<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'request',
        'error_message',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'request' => 'array', // Untuk manipulasikan data JSON
    ];

    // Relasi dengan model Application
    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}