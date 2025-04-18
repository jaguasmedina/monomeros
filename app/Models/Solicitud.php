<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Solicitud extends Model
{
    protected $table = 'solicitudes';
    protected $fillable = [
        'tipo_persona',
        'fecha_registro',
        'razon_social',
        'tipo_id',
        'identificador',
        'motivo',
        'nombre_completo',
        'archivo',
        'tipo_cliente',
        'estado',
        'admin_id'
    ];

    protected $casts = [
        'fecha_registro' => 'date',
        'archivo' => 'array'
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin::class, 'admin_id');
    }

    public function getArchivosAttribute()
    {
        return $this->archivo ? json_decode($this->archivo) : [];
    }

    public function miembros()
{
    return $this->hasMany(\App\Models\Miembro::class);
}

}