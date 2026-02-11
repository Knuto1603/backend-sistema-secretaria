<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramacionAcademica extends Model
{
    use HasUuids;

    protected $table = 'programacion_academica';

    protected $fillable = [
        'curso_id',
        'periodo_id',
        'docente_id',
        'clave',
        'grupo',
        'seccion',
        'aula',
        'n_acta',
        'capacidad',
        'n_inscritos'
    ];

    protected $casts = [
        'capacidad' => 'integer',
        'n_inscritos' => 'integer',
    ];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class);
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class);
    }

    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'programacion_id');
    }
}
