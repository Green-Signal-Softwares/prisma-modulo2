<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'atendente_id', 'tag_id', 'title', 'description', 'status', 'ticket_number', 'file_path'])]
class Solicitation extends Model
{
    use HasFactory;

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    protected $casts = [
        'file_path' => 'array',
    ];

    /**
     * Get the user that owns the solicitation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the atendente assigned to this solicitation.
     */
    public function atendente()
    {
        return $this->belongsTo(User::class, 'atendente_id');
    }

    /**
     * Get the messages for the solicitation.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Checklists de fechamento associados ao chamado.
     */
    public function checklists()
    {
        return $this->hasMany(SolicitationChecklist::class);
    }

    /**
     * Avaliações feitas pelo cliente para este chamado.
     */
    public function evaluations()
    {
        return $this->hasMany(SolicitationEvaluation::class);
    }
}
