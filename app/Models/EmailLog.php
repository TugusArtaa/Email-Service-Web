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
        'request' => 'array', // Allows easy JSON manipulation
    ];

    /**
     * Relationship to Application.
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}