<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoSolicitud extends Model
{
    use HasFactory;

    protected $table = 'movimientos_solicitudes';

    protected $fillable = [
        'solicitud_id',
        'estado_anterior',
        'estado_nuevo',
        'comentario',
        'fecha_movimiento',
    ];

    /**
     * Relación con la tabla solicitudes (muchos movimientos pertenecen a una sola solicitud).
     */
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id');
    }
}
