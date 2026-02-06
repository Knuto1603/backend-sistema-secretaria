<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Solicitud extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'solicitud';

    /**
     * Los atributos que son asignables.
     */
    protected $fillable = [
        'user_id',
        'tipo_solicitud_id',
        'programacion_id',
        'metadatos',
        'motivo',
        'estado',
        'firma_digital_path',
        'archivo_sustento_path',
        'asignado_a',
        'observaciones_admin'
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'metadatos' => 'array', // Permite manejar el JSON como un array de PHP
    ];

    /**
     * Relación: La solicitud pertenece a un usuario (estudiante).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación: La solicitud tiene un tipo específico (Cupo Especial, etc).
     */
    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoSolicitud::class, 'tipo_solicitud_id');
    }

    /**
     * Relación: La solicitud está vinculada a un curso programado.
     */
    public function programacion(): BelongsTo
    {
        return $this->belongsTo(ProgramacionAcademica::class, 'programacion_id');
    }

    /**
     * Relación: La solicitud puede ser asignada a un administrativo para revisión.
     */
    public function asignado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }
}
