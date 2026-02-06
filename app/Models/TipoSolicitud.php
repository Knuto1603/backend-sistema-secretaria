<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoSolicitud extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'tipo_solicitudes';

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'requiere_archivo',
        'activo'
    ];

    /**
     * Relación: Un tipo de solicitud puede tener muchas solicitudes registradas.
     */
    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'tipo_solicitud_id');
    }
}
