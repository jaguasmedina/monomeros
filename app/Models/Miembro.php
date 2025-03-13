<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Miembro extends Model
{
    use HasFactory;

    protected $fillable = [
        'solicitud_id',
        'titulo',
        'nombre',
        'tipo_id',
        'numero_id',
        'favorable',
        'concepto_no_favorable',
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

}
