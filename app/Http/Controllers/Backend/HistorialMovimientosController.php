<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use App\Models\MovimientoSolicitud;
use Illuminate\Support\Facades\Auth;

class HistorialMovimientosController extends Controller
{
    public function __construct()
    {
        // Solo superadmin (o el rol que necesites)
        $this->middleware('role:superadmin');
    }

    /**
     * Mostrar historial de movimientos de una solicitud.
     */
    public function index(Request $request): Renderable
    {
        // Verifica permisos
        $this->checkAuthorization(Auth::user(), ['admin.view']);

        $numeroSolicitud = $request->input('numero_solicitud');
        $movimientos = collect();

        if ($numeroSolicitud) {
            $movimientos = MovimientoSolicitud::with('solicitud')
                ->where('solicitud_id', $numeroSolicitud)
                // Orden cronológico ascendente por created_at
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('backend.pages.movimientos.index', compact('movimientos', 'numeroSolicitud'));
    }
}
