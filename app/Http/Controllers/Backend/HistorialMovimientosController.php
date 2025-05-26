<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use App\Models\MovimientoSolicitud;
use Carbon\Carbon;

class HistorialMovimientosController extends Controller
{
    /**
     * Aplicar middleware en el constructor para restringir acceso.
     */
    public function __construct()
    {
        // Sólo superadmin puede ver el historial
        $this->middleware(['role:superadmin']);
    }

    /**
     * Mostrar la lista de movimientos de una solicitud.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request): Renderable
    {
        $numeroSolicitud = $request->input('numero_solicitud');
        $movimientos = collect();

        if ($numeroSolicitud) {
            $movimientos = MovimientoSolicitud::with('solicitud')
                ->where('solicitud_id', $numeroSolicitud)
                ->orderBy('fecha_movimiento', 'asc') // o por created_at si prefieres
                ->get();
        }

        return view('backend.pages.movimientos.index', compact('movimientos', 'numeroSolicitud'));
    }
}
