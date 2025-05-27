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
use Illuminate\Support\Facades\Mail;
use App\Mail\SolicitudStatusChanged;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MiembrosExport;

class AnalistsController extends Controller
{
    use LogsActivity;

    public function __construct()
    {
        // Sólo usuarios con permiso 'admin.view' pueden acceder
        $this->middleware('can:admin.view');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['titulo', 'nombre', 'tipo_id', 'numero_id', 'favorable', 'observaciones'])
            ->useLogName('miembros')
            ->setDescriptionForEvent(fn(string $eventName) => "Acción: {$eventName} en un miembro")
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /** Bandeja de analistas (solicitudes ENTREGADAS por usuarios) */
    public function index(): Renderable
    {
        $solicitudes = Solicitud::where('estado', 'enviado')->get();
        return view('backend.pages.analists.index', compact('solicitudes'));
    }

    /** Reporte y exportación de miembros (igual que antes) */
    public function report(Request $request): Renderable
    {
        $filters = $request->only(['nombre', 'tipo_id', 'favorable']);
        $query = Miembro::query();
        if (!empty($filters['nombre'])) {
            $query->where('nombre','like','%'.$filters['nombre'].'%');
        }
        if (!empty($filters['tipo_id'])) {
            $query->where('tipo_id',$filters['tipo_id']);
        }
        if (!empty($filters['favorable'])) {
            $query->where('favorable',$filters['favorable']);
        }
        $miembros = $query->get();
        return view('backend.pages.reports.miembros', compact('miembros','filters'));
    }

    public function exportReport(Request $request)
    {
        $filters = $request->only(['nombre','tipo_id','favorable']);
        return Excel::download(new MiembrosExport($filters), 'miembros.xlsx');
    }

    /** Mostrar formulario de revisión de una solicitud */
    public function show($id): Renderable
    {
        $solicitud = Solicitud::with('miembros')->findOrFail($id);
        return view('backend.pages.analists.show', compact('solicitud'));
    }

    /** Eliminar miembro vía AJAX */
    public function eliminarMiembro($id)
    {
        $miembro = Miembro::find($id);
        if (!$miembro) {
            return response()->json(['success'=>false,'message'=>'Miembro no encontrado'],404);
        }
        $miembro->delete();
        return response()->json(['success'=>true,'message'=>'Miembro eliminado correctamente']);
    }

    /**
     * Procesar (botón "Procesar"):
     * - Inserta/actualiza miembros
     * - Siempre envía la solicitud a SAGRILAFT (estado APROBADOR_SAGRILAFT)
     */
    public function save(Request $request, $id): RedirectResponse
    {
        // 1) Validación de los datos de miembros
        $request->validate([
            'miembros.*.titulo'        => 'required|string|max:100',
            'miembros.*.nombre'        => 'required|string|max:100',
            'miembros.*.tipo_id'       => 'required|string',
            'miembros.*.numero_id'     => 'required|string',
            'miembros.*.favorable'     => 'required|string',
            'miembros.*.observaciones' => 'nullable|string',
        ]);

        // 2) Reemplazo completo de miembros
        Miembro::where('solicitud_id',$id)->delete();
        foreach ($request->miembros as $data) {
            Miembro::create([
                'solicitud_id'         => $id,
                'titulo'               => $data['titulo'],
                'nombre'               => $data['nombre'],
                'tipo_id'              => $data['tipo_id'],
                'numero_id'            => $data['numero_id'],
                'favorable'            => $data['favorable'],
                'observaciones'        => $data['observaciones'] ?? null,
                'concepto_no_favorable'=> null, // este flujo ignora motivos de no favorable
            ]);
        }

        // 3) Actualizar estado a SAGRILAFT
        Solicitud::where('id',$id)
            ->update(['estado'=>'APROBADOR_SAGRILAFT']);

        // 4) Notificar por correo al creador
        $sol = Solicitud::findOrFail($id);
        Mail::to($sol->admin->email)
            ->send(new SolicitudStatusChanged($sol));

        return redirect()
            ->route('admin.analists.index')
            ->with('success','Solicitud enviada a SAGRILAFT correctamente');
    }

    /**
     * Regresar por Documentación (botón "Regresar por Documentación"):
     * - Ignores miembros
     * - Actualiza estado a DOCUMENTACION
     */
    public function savenf(Request $request, $id): RedirectResponse
    {
        Solicitud::where('id',$id)
            ->update(['estado'=>'DOCUMENTACION']);

        $sol = Solicitud::findOrFail($id);
        Mail::to($sol->admin->email)
            ->send(new SolicitudStatusChanged($sol));

        return redirect()
            ->route('admin.analists.index')
            ->with('success','Solicitud regresada a Documentación correctamente');
    }
}
