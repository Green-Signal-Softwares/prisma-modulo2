<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preset extends Model
{
    protected $fillable = [
        'shortcut',
        'title',
        'text',
        'user_id',
        'parent_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Preset::class, 'parent_id');
    }

    public function overrides()
    {
        return $this->hasMany(Preset::class, 'parent_id');
    }
}
