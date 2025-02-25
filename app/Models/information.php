<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class information extends Model
{
    use HasFactory;

    protected $table = 'informacion';

    protected $primaryKey = 'identificador';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'identificador',
        'tipo',
        'nombre_completo',
        'empresa',
        'fecha_registro',
        'fecha_vigencia',
        'cargo',
        'estado',
    ];

    public $timestamps = true;
}
