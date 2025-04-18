<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\Solicitud;
use App\Models\Miembro;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MiembrosExport;

class AnalistsController extends Controller
{
    public function index(): Renderable
    {   $this->checkAuthorization(auth()->user(), ['admin.view']);
        $solicitudes = Solicitud::where('estado', 'enviado')->get();
        return view('backend.pages.analists.index', [
            'solicitudes' => $solicitudes,
        ]);
    }


    public function report(Request $request): Renderable
    {
        // Recoger filtros (por ejemplo, nombre, tipo_id y favorable)
        $filters = $request->only(['nombre', 'tipo_id', 'favorable']);
    
        // Construir la consulta sobre la tabla miembros
        $query = \App\Models\Miembro::query();
        if (!empty($filters['nombre'])) {
            $query->where('nombre', 'like', '%' . $filters['nombre'] . '%');
        }
        if (!empty($filters['tipo_id'])) {
            $query->where('tipo_id', $filters['tipo_id']);
        }
        if (!empty($filters['favorable'])) {
            $query->where('favorable', $filters['favorable']);
        }
        $miembros = $query->get();
    
        return view('backend.pages.reports.miembros', compact('miembros', 'filters'));
    }
    

    public function exportReport(Request $request)
{
    $filters = $request->only(['nombre', 'tipo_id', 'favorable']);
    return Excel::download(new MiembrosExport($filters), 'miembros.xlsx');
}



    public function show($id)
    {
        $solicitud = Solicitud::with('miembros')->findOrFail($id);
        return view('backend.pages.analists.show', [
            'solicitud' => $solicitud,
        ]);
    }
    public function eliminarMiembro($id)
    {
        $miembro = Miembro::find($id);

        if (!$miembro) {
            return response()->json(['success' => false, 'message' => 'Miembro no encontrado'], 404);
        }

        $miembro->delete();

        return response()->json(['success' => true, 'message' => 'Miembro eliminado correctamente']);
    }
    public function save(Request $request, $id){
        $request->validate([
            'miembros.*.titulo' => 'required|string|max:100',
            'miembros.*.nombre' => 'required|string|max:100',
            'miembros.*.tipo_id' => 'required|string',
            'miembros.*.numero_id' => 'required|string',
            'miembros.*.favorable' => 'required|string',
        ]);

        $actualizarSolicitud = false;
        $motivoRechazo = null;
        $estado = 'aprobador_SAGRILAFT';

        if (!empty($request->miembros) && is_array($request->miembros)) {
            Miembro::where('solicitud_id', $id)->delete();
            foreach ($request->miembros as $miembroData) {
                Miembro::create([
                    'solicitud_id' => $id,
                    'titulo' => $miembroData['titulo'],
                    'nombre' => $miembroData['nombre'],
                    'tipo_id' => $miembroData['tipo_id'],
                    'numero_id' => $miembroData['numero_id'],
                    'favorable' => $miembroData['favorable'],
                    'concepto_no_favorable' => $request->concepto_no_favorable
                ]);
            }

            if ($miembroData['favorable'] === "no") {
                $actualizarSolicitud = true;
                $motivoRechazo = $request->concepto_no_favorable ?? "No especificado";
                $estado = 'documentacion';
            }else{
                $estado = 'aprobador_SAGRILAFT';
            }
        }
        if ($actualizarSolicitud) {
            Solicitud::where('id', $id)->update([
                'motivo' => $motivoRechazo,
                'estado' => $estado
            ]);
        }else{
            Solicitud::where('id', $id)->update([
                'estado' => $estado
            ]);
        }

        return redirect()->back()->with('success', 'Solicitud procesada correctamente.');
    }
    
                public function savenf(Request $request, $id)
            {
                // Se asume que para actualizar la solicitud en este flujo (No Favorable)
                // se requiere un valor para el campo 'motivo'. Si no se recibe, se asigna un valor por defecto.
                $motivoRechazo = $request->input('concepto_no_favorable');
                if (empty($motivoRechazo)) {
                    $motivoRechazo = 'Sin motivo'; // valor por defecto para evitar null
                }
                
                $razonDocumentacion = $request->input('razon_documentacion');
                $numeroSolicitud = $request->input('numero_solicitud'); // si lo necesitas, aunque no se use en el update

                // Actualizamos la solicitud con los nuevos valores
                Solicitud::where('id', $id)->update([
                    'motivo' => $motivoRechazo,
                    'estado' => $razonDocumentacion
                ]);

                return redirect()->back()->with('success', 'Solicitud actualizada correctamente.');
            }


}
