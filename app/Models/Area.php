<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// --- MODELO AREA ---
class Area extends Model {
    protected $fillable = ['nombre'];
    public function cursos(): HasMany { return $this->hasMany(Curso::class); }
}
