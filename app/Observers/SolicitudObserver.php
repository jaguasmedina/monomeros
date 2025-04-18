<?php

namespace App\Observers;

use App\Models\Solicitud;
use App\Models\MovimientoSolicitud;
use Carbon\Carbon;

class SolicitudObserver
{
    /**
     * Se ejecuta cuando se crea una nueva Solicitud.
     * Aquí se registra el primer movimiento (por ejemplo, "enviado").
     *
     * @param Solicitud $solicitud
     */
    public function created(Solicitud $solicitud)
    {
        MovimientoSolicitud::create([
            'solicitud_id'    => $solicitud->id,
            'estado_anterior' => null, // No hay estado anterior en la creación
            'estado_nuevo'    => $solicitud->estado,
            'comentario'      => 'Solicitud creada',
            'fecha_movimiento'=> Carbon::now(),
        ]);
    }

    /**
     * Se ejecuta cuando se actualiza una Solicitud.
     * Si el campo "estado" ha cambiado, se registra el movimiento.
     *
     * @param Solicitud $solicitud
     */
    public function updated(Solicitud $solicitud)
    {
        if ($solicitud->isDirty('estado')) {
            $estadoAnterior = $solicitud->getOriginal('estado');
            $estadoNuevo   = $solicitud->estado;
            
            MovimientoSolicitud::create([
                'solicitud_id'    => $solicitud->id,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo'    => $estadoNuevo,
                'comentario'      => '', // Puedes ajustar para agregar comentarios específicos aquí
                'fecha_movimiento'=> Carbon::now(),
            ]);
        }
    }
}
