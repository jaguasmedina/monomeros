<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;

class Information extends Model
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

        protected static $logAttributes = ['tipo', 'nombre_completo', 'empresa', 'estado'];

        protected static $logOnlyDirty = true;

        protected static $logName = 'informacion';

        public function getDescriptionForEvent(string $eventName): string
        {
            return "El usuario " . auth()->user()->name . " ha realizado la acción: {$eventName} en información.";
        }
}
