<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'solicitation_id',
        'user_id',
        'text',
        'file_path',
        'file_name',
        'parent_id',
        'reactions',
        'read_at',
        'type',
        'metadata',
    ];

    protected $casts = [
        'reactions' => 'array',
        'read_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function solicitation()
    {
        return $this->belongsTo(Solicitation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_id');
    }
}
