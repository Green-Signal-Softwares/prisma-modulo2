<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nivel_n1',
        'nivel_n2',
        'fila',
    ];

    protected $casts = [
        'nivel_n1' => 'boolean',
        'nivel_n2' => 'boolean',
        'fila' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
