<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// --- MODELO CURSO ---
class Curso extends Model {
    protected $fillable = ['codigo', 'nombre', 'area_id'];
    public function area(): BelongsTo { return $this->belongsTo(Area::class); }
    public function programaciones(): HasMany { return $this->hasMany(ProgramacionAcademica::class); }
}
