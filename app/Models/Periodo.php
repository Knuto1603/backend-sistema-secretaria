<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// --- MODELO PERIODO ---
class Periodo extends Model {
    protected $fillable = ['nombre', 'activo'];
    public function programaciones(): HasMany { return $this->hasMany(ProgramacionAcademica::class); }
}
