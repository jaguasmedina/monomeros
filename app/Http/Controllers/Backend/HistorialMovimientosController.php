<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\MovimientoSolicitud;
use Illuminate\Http\Request;

class HistorialMovimientosController extends Controller
{
    public function index(Request $request)
    {
        // Asegurar que sólo superadmin tenga acceso
        $this->middleware(['role:superadmin']);

        // Tomar el número de la solicitud (ID) desde un input "numero_solicitud"
        $numeroSolicitud = $request->input('numero_solicitud');
        $movimientos = collect();

        if ($numeroSolicitud) {
            // Filtrar movimientos por la solicitud_id (asumiendo que "número de la solicitud" es el ID de la tabla solicitudes)
            $movimientos = MovimientoSolicitud::with('solicitud')
                ->where('solicitud_id', $numeroSolicitud)
                ->orderBy('fecha_movimiento', 'asc')
                ->get();
        }

        return view('backend.pages.movimientos.index', compact('movimientos', 'numeroSolicitud'));
    }
}
