<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// --- MODELO PROGRAMACIÓN ACADÉMICA (El principal) ---
class ProgramacionAcademica extends Model {
    protected $table = 'programacion_academica';
    protected $fillable = [
        'curso_id', 'periodo_id', 'docente_id', 'clave',
        'grupo', 'seccion', 'aula', 'n_acta', 'capacidad', 'n_inscritos'
    ];

    public function curso(): BelongsTo { return $this->belongsTo(Curso::class); }
    public function periodo(): BelongsTo { return $this->belongsTo(Periodo::class); }
    public function docente(): BelongsTo { return $this->belongsTo(Docente::class); }
}
