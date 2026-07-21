<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternalNote extends Model
{
    protected $fillable = [
        'solicitation_id',
        'user_id',
        'content',
        'is_pinned',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    /**
     * The solicitation this note belongs to.
     */
    public function solicitation()
    {
        return $this->belongsTo(Solicitation::class);
    }

    /**
     * The staff member who wrote this note.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
