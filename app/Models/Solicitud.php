<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'tipo_persona',
        'fecha_registro',
        'razon_social',
        'tipo_id',
        'identificador',
        'motivo',
        'nombre_completo',
        'tipo_visitante',
        'archivo',
        'tipo_cliente'
    ];
}
