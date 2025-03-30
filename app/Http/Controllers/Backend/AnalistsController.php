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

class AnalistsController extends Controller
{
    public function index(): Renderable
    {   $this->checkAuthorization(auth()->user(), ['admin.view']);
        $solicitudes = Solicitud::where('estado', 'enviado')->get();
        return view('backend.pages.analists.index', [
            'solicitudes' => $solicitudes,
        ]);
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
        $estado = 'aprobador';

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
                $estado = 'aprobador';
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
    public function savenf(Request $request, $id){

            $actualizarSolicitud = true;
            $motivoRechazo = $request->concepto_no_favorable;
            $razonDocumentacion = $request->razon_documentacion ;
            $numeroSolicitud = $request->numero_solicitud ;

            Solicitud::where('id', $id)->update([
                'motivo' => $motivoRechazo,
                'estado' => $razonDocumentacion
            ]);

        return redirect()->back()->with('success', 'Solicitud actualizada correctamente.');
    }
}
