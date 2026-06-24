<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitationEvaluation extends Model
{
    protected $fillable = [
        'solicitation_id',
        'user_id',
        'nota',
        'problema_resolvido',
        'comentario',
    ];

    protected $casts = [
        'problema_resolvido' => 'boolean',
    ];

    public function solicitation()
    {
        return $this->belongsTo(Solicitation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
