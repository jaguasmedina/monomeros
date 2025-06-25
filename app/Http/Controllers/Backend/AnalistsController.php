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

    public function index(): Renderable
    {
        $solicitudes = Solicitud::where('estado', 'enviado')->get();
        return view('backend.pages.analists.index', compact('solicitudes'));
    }

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

    public function show($id): Renderable
    {
        $solicitud = Solicitud::with('miembros')->findOrFail($id);
        return view('backend.pages.analists.show', compact('solicitud'));
    }

    public function eliminarMiembro($id)
    {
        $miembro = Miembro::find($id);
        if (!$miembro) {
            return response()->json(['success'=>false,'message'=>'Miembro no encontrado'],404);
        }
        $miembro->delete();
        return response()->json(['success'=>true,'message'=>'Miembro eliminado correctamente']);
    }

    public function save(Request $request, $id): RedirectResponse
    {
        $accion = $request->input('accion');

        // Validar y guardar miembros solo si no es documentacion
        if ($accion !== 'documentacion') {
            $request->validate([
                'miembros.*.titulo'        => 'required|string|max:100',
                'miembros.*.nombre'        => 'required|string|max:100',
                'miembros.*.tipo_id'       => 'required|string',
                'miembros.*.numero_id'     => 'required|string',
                'miembros.*.favorable'     => 'required|string',
                'miembros.*.observaciones' => 'nullable|string',
            ]);

            Miembro::where('solicitud_id', $id)->delete();

            if ($request->has('miembros')) {
                foreach ($request->miembros as $data) {
                    Miembro::create([
                        'solicitud_id'         => $id,
                        'titulo'               => $data['titulo'] ?? '',
                        'nombre'               => $data['nombre'] ?? '',
                        'tipo_id'              => $data['tipo_id'] ?? '',
                        'numero_id'            => $data['numero_id'] ?? '',
                        'favorable'            => $data['favorable'] ?? 'si',
                        'observaciones'        => $data['observaciones'] ?? null,
                        'concepto_no_favorable'=> null,
                    ]);
                }
            }
        }

        if ($accion === 'procesar') {
            Solicitud::where('id', $id)->update(['estado' => 'APROBADOR_SAGRILAFT']);
            $sol = Solicitud::findOrFail($id);
            //Mail::to($sol->admin->email)->send(new SolicitudStatusChanged($sol)); // correo desactivado
            return redirect()->route('admin.analists.index')->with('success', 'Solicitud enviada a SAGRILAFT correctamente.');
        }

                    if ($accion === 'documentacion') {
                    // 1) Actualiza estado y motivo de devolución
                    Solicitud::where('id', $id)->update([
                        'estado'         => 'DOCUMENTACION',
                        'motivo_rechazo' => $request->input('motivo_rechazo'),
                    ]);

                    // 2) Recupera la solicitud ya con motivo_rechazo
                    $sol = Solicitud::findOrFail($id);

                    // 3) Envía el correo de notificación
                    Mail::to($sol->admin->email)
                        ->send(new SolicitudStatusChanged($sol));

                    // 4) Redirige y sale del método
                    return redirect()
                        ->route('admin.analists.index')
                        ->with('success', 'Solicitud regresada a Documentación correctamente.');
                }   // <-- Esta llave cierra sólo el `if`

                // Si no es "documentacion", sigue con el flujo de "borrador"
                return redirect()
                    ->route('admin.analists.show', $id)
                    ->with('success', 'Borrador guardado correctamente.');
    }   // <-- Y ésta cierra todo el método save()

}

