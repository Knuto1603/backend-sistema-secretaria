<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// --- MODELO DOCENTE ---
class Docente extends Model {
    protected $fillable = ['nombre_completo'];
    public function programaciones(): HasMany { return $this->hasMany(ProgramacionAcademica::class); }
}
