<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'send_to',
        'type',
        'status',
        'start_at',
        'end_at',
        'sent_at',
        'title',
        'content',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'sent_at' => 'datetime',
    ];
}
