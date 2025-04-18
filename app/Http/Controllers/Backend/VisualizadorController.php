<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use App\Models\Information;

class VisualizadorController extends Controller
{
    // Puedes aplicar un middleware para que solo usuarios con rol "visualizador" puedan acceder.
    public function __construct()
    {
        $this->middleware('role:visualizador');
    }

    /**
     * Muestra la vista para que el usuario visualice la información filtrada.
     */
    public function report(Request $request): Renderable
    {
        // Recoger los filtros de la petición (por GET)
        // Parámetros: numero_solicitud, nombre y identificador
        $filters = $request->only(['numero_solicitud', 'nombre', 'identificador']);

        $query = Information::query();
        // Filtrar "numero_solicitud" en este caso se interpretará como búsqueda parcial en el campo "identificador"
        if (!empty($filters['numero_solicitud'])) {
            $query->where('identificador', 'like', '%' . $filters['numero_solicitud'] . '%');
        }
        // Filtrar "nombre": buscar en "nombre_completo" o "empresa"
        if (!empty($filters['nombre'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nombre_completo', 'like', '%' . $filters['nombre'] . '%')
                  ->orWhere('empresa', 'like', '%' . $filters['nombre'] . '%');
            });
        }
        // Filtrar "identificador" de forma exacta
        if (!empty($filters['identificador'])) {
            $query->where('identificador', $filters['identificador']);
        }
        $informacion = $query->get();

        return view('backend.pages.visualizador', compact('informacion', 'filters'));
    }
}
