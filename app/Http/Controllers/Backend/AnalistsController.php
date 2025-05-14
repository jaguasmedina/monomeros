<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\Solicitud;
use App\Models\Miembro;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MiembrosExport;

use Illuminate\Support\Facades\Mail;
use App\Mail\SolicitudStatusChanged;  // (o el nombre que le hayas dado)


class AnalistsController extends Controller
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['titulo', 'nombre', 'tipo_id', 'numero_id', 'favorable', 'concepto_no_favorable'])
            ->useLogName('miembros')
            ->setDescriptionForEvent(fn(string $eventName) => "Se ha realizado la acción: {$eventName} en un miembro")
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function index(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.view']);
        $solicitudes = Solicitud::where('estado', 'enviado')->get();
        return view('backend.pages.analists.index', compact('solicitudes'));
    }

    public function report(Request $request): Renderable
    {
        $filters = $request->only(['nombre', 'tipo_id', 'favorable']);
        $query = Miembro::query();
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
        return view('backend.pages.analists.show', compact('solicitud'));
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

    public function save(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'miembros.*.titulo'               => 'required|string|max:100',
            'miembros.*.nombre'               => 'required|string|max:100',
            'miembros.*.tipo_id'              => 'required|string',
            'miembros.*.numero_id'            => 'required|string',
            'miembros.*.favorable'            => 'required|string',
            'miembros.*.observaciones'        => 'nullable|string',        // nuevo campo
        ]);

        $actualizarSolicitud = false;
        $motivoRechazo = null;
        $estado = 'aprobador_SAGRILAFT';

        if (!empty($request->miembros) && is_array($request->miembros)) {
            Miembro::where('solicitud_id', $id)->delete();
            foreach ($request->miembros as $miembroData) {
                Miembro::create([
                    'solicitud_id'           => $id,
                    'titulo'                 => $miembroData['titulo'],
                    'nombre'                 => $miembroData['nombre'],
                    'tipo_id'                => $miembroData['tipo_id'],
                    'numero_id'              => $miembroData['numero_id'],
                    'favorable'              => $miembroData['favorable'],
                    'concepto_no_favorable'  => $request->concepto_no_favorable,
                    'observaciones'          => $miembroData['observaciones'] ?? null, // guardamos observaciones
                ]);

                if ($miembroData['favorable'] === "no") {
                    $actualizarSolicitud = true;
                    $motivoRechazo = $request->concepto_no_favorable ?? "No especificado";
                    $estado = 'documentacion';
                }
            }
        }

                        $datos = ['estado' => $estado];
                if ($actualizarSolicitud) {
                    $datos['motivo'] = $motivoRechazo;
                }
                Solicitud::where('id', $id)->update($datos);

        return redirect()->back()->with('success', 'Solicitud procesada correctamente.');
    }

    public function savenf(Request $request, $id): RedirectResponse
    {
        $motivoRechazo     = $request->input('concepto_no_favorable', 'Sin motivo');
        $razonDocumentacion = $request->input('razon_documentacion');

        Solicitud::where('id', $id)->update([
            'motivo' => $motivoRechazo,
            'estado' => $razonDocumentacion,
        ]);

        // recarga el modelo para tener el estado actualizado
        $solicitud = Solicitud::findOrFail($id);

        // envía el correo al creador
        Mail::to($solicitud->admin->email)
            ->send(new SolicitudStatusChanged($solicitud));

        return redirect()->back()->with('success', 'Solicitud actualizada correctamente.');
    }
}
