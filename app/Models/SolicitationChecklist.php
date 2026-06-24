<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitationChecklist extends Model
{
    protected $fillable = [
        'solicitation_id',
        'atendente_id',
        'category',
        'problema_identificado',
        'solucao_aplicada',
        'encaminhamento',
        'descricao',
    ];

    public function solicitation()
    {
        return $this->belongsTo(Solicitation::class);
    }

    public function atendente()
    {
        return $this->belongsTo(User::class, 'atendente_id');
    }
}
