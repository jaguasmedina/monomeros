<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Solicitud;
use Carbon\Carbon;
use DB;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Validator;
// Added for report/export
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SolicitudesExport;

class UserServiceController extends Controller
{
    use LogsActivity;

    public function __construct()
    {
        $this->middleware('auth:admin');

        // Redirige a la vista para crear solicitud cuando el usuario tiene el rol "usuarios"
        $this->middleware(function ($request, $next) {
            if (auth('admin')->check() &&
                auth('admin')->user()->hasRole('usuarios') &&
                ($request->is('admin') || $request->is('admin/'))) {
                return redirect()->route('admin.service.request');
            }
            return $next($request);
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'created_at', 'updated_at'])
            ->useLogName('Information')
            ->setDescriptionForEvent(fn(string $eventName) => "Se ha realizado la acción: {$eventName} en un usuario")
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // 1) Mostrar formulario de nueva solicitud
    public function request(): Renderable
    {
        $this->authorize('admin.view');
        return view('backend.pages.requests.request');
    }

    // 2) Mostrar reporte de Solicitudes con filtro por estado real
    public function report(Request $request): Renderable
    {
        $this->authorize('admin.view');

        // 1) Recogemos filtros anteriores
        $filters = $request->only(['fecha_inicio', 'fecha_fin', 'razon_social', 'estado']);

        // 2) Construimos la consulta aplicando los filtros
        $query = Solicitud::query();
        if (!empty($filters['fecha_inicio'])) {
            $query->where('fecha_registro', '>=', $filters['fecha_inicio']);
        }
        if (!empty($filters['fecha_fin'])) {
            $query->where('fecha_registro', '<=', $filters['fecha_fin']);
        }
        if (!empty($filters['razon_social'])) {
            $query->where('razon_social', 'like', '%' . $filters['razon_social'] . '%');
        }
        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }
        $solicitudes = $query->get();

        // 3) Sacamos lista de todos los estados existentes para el dropdown
        $estados = Solicitud::select('estado')
                            ->distinct()
                            ->orderBy('estado')
                            ->pluck('estado');

        // 4) Devolvemos la vista con las variables
        return view('backend.pages.reports.solicitudes', compact(
            'solicitudes',
            'filters',
            'estados'
        ));
    }

    // 3) Exportar reporte de Solicitudes a Excel
    public function exportReport(Request $request)
    {
        $this->authorize('admin.view');

        $filters = $request->only(['fecha_inicio', 'fecha_fin', 'razon_social', 'estado']);
        return Excel::download(new SolicitudesExport($filters), 'solicitudes.xlsx');
    }

    // 4) Procesar consulta de solicitudes (Query)
    public function handleQuery(Request $request): Renderable
    {
        $this->authorize('admin.view');

        if ($request->isMethod('post')) {
            $request->validate([
                'numero_solicitud' => 'nullable|integer',
                'identificador'    => 'nullable|string|max:50',
            ]);

            $solicitud = Solicitud::when($request->numero_solicitud, function ($query, $numero) {
                    return $query->where('id', $numero);
                })
                ->when($request->identificador, function ($query, $id) {
                    return $query->where('identificador', strtoupper($id));
                })
                ->get();

            return view('backend.pages.requests.query', [
                'solicitud'         => $solicitud,
                'numero_solicitud'  => $request->numero_solicitud,
                'identificador'     => $request->identificador
            ]);
        }

        return view('backend.pages.requests.query', [
            'solicitud'         => collect(),
            'numero_solicitud'  => null,
            'identificador'     => null
        ]);
    }

    // 5) Almacenar nueva solicitud
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'tipo_persona'    => 'required|in:natural,juridica',
            'fecha_registro'  => 'required|date',
            'razon_social'    => 'required|string|max:255',
            'tipo_id'         => 'required|string|max:50',
            'identificador'   => 'required|string|max:50',
            'motivo'          => 'required|string',
            'nombre_completo' => $request->tipo_persona === 'natural' ? 'required|string|max:255' : 'nullable',
            'archivos'        => $request->tipo_persona === 'juridica' ? 'required|array|min:1|max:10' : 'nullable',
            'archivos.*'      => 'file|mimes:pdf|max:2048',
            'tipo_cliente'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $archivosPaths = [];
        if ($request->tipo_persona === 'juridica' && $request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                $originalName = str_replace(' ', '_', $archivo->getClientOriginalName());
                $filename = time() . '_' . $originalName;
                $path = $archivo->storeAs('solicitudes/' . date('Y/m'), $filename, 'public');
                $archivosPaths[] = $path;
            }
        }

        $solicitud = Solicitud::create([
            'tipo_persona'    => $request->tipo_persona,
            'fecha_registro'  => $request->fecha_registro,
            'razon_social'    => strtoupper($request->razon_social),
            'tipo_id'         => $request->tipo_id,
            'identificador'   => strtoupper($request->identificador),
            'motivo'          => strtoupper($request->motivo),
            'nombre_completo' => $request->tipo_persona === 'natural' ? strtoupper($request->nombre_completo) : null,
            'archivo'         => !empty($archivosPaths) ? json_encode($archivosPaths) : null,
            'tipo_cliente'    => $request->tipo_cliente,
            'estado'          => 'enviado',
            'admin_id'        => Auth::guard('admin')->id(),
        ]);

        return redirect()->route('admin.service.request')
            ->with('success', 'Solicitud creada exitosamente')
            ->with('solicitud_id', $solicitud->id);
    }

    // 6) Mostrar formulario de edición
    public function edit($id): Renderable
    {
        $this->authorize('admin.edit');
        $solicitud = Solicitud::findOrFail($id);
        return view('backend.pages.requests.edit', compact('solicitud'));
    }

    // 7) Guardar edición
    public function update(Request $request): RedirectResponse
    {
        $this->authorize('admin.edit');

        $request->validate([
            'id'              => 'required|exists:solicitudes,id',
            'tipo_persona'    => 'required|in:natural,juridica',
            'fecha_registro'  => 'required|date',
            'razon_social'    => 'required|string|max:255',
            'tipo_id'         => 'required|string|max:50',
            'identificador'   => 'required|string|max:50',
            'motivo'          => 'required|string',
            'nombre_completo' => 'nullable|string|max:255',
            'archivos'        => 'nullable|array|min:1|max:10',
            'archivos.*'      => 'file|mimes:pdf|max:2048',
            'tipo_cliente'    => 'nullable|string|in:proveedor,cliente,visitante,contratista',
        ]);

        $solicitud = Solicitud::findOrFail($request->id);

        // Manejar múltiples archivos
        if ($request->hasFile('archivos')) {
            if ($solicitud->archivo) {
                $previos = json_decode($solicitud->archivo, true);
                if (is_array($previos)) {
                    foreach ($previos as $archivoPrevio) {
                        Storage::disk('public')->delete($archivoPrevio);
                    }
                }
            }
            $archivosPaths = [];
            foreach ($request->file('archivos') as $archivo) {
                $originalName = str_replace(' ', '_', $archivo->getClientOriginalName());
                $filename = time() . '_' . $originalName;
                $path = $archivo->storeAs('solicitudes/' . date('Y/m'), $filename, 'public');
                $archivosPaths[] = $path;
            }
            $solicitud->archivo = json_encode($archivosPaths);
        }

        $solicitud->update([
            'tipo_persona'    => $request->tipo_persona,
            'fecha_registro'  => $request->fecha_registro,
            'razon_social'    => strtoupper($request->razon_social),
            'tipo_id'         => $request->tipo_id,
            'identificador'   => strtoupper($request->identificador),
            'motivo'          => strtoupper($request->motivo),
            'nombre_completo' => strtoupper($request->nombre_completo ?? ''),
            'tipo_cliente'    => $request->tipo_cliente,
            'estado'          => 'enviado',
        ]);

        return redirect()->route('admin.service.query')
            ->with('success', 'Solicitud actualizada exitosamente')
            ->with('solicitud_id', $solicitud->id);
    }

    // 8) “Mis Solicitudes” (rol usuarios)
    public function misSolicitudes(Request $request): Renderable
    {
        $adminId = Auth::guard('admin')->id();

        $solicitudes = Solicitud::where('admin_id', $adminId)
            ->with(['admin' => function($q) {
                $q->select('id','name');
            }])
            ->orderBy('created_at','desc')
            ->get();

        Log::debug('Solicitudes del usuario', [
            'admin_id' => $adminId,
            'count'    => $solicitudes->count(),
            'ids'      => $solicitudes->pluck('id')
        ]);

        return view('backend.pages.solicitudes.mis_solicitudes', compact('solicitudes'));
    }

    // 9) Generar PDF final
    public function generarDocumentoFinal($id)
    {
        $this->authorize('admin.view');

        $solicitud      = Solicitud::findOrFail($id);
        $logoPath       = storage_path('app/public/logo.png');
        $rqDynamicPath  = storage_path('app/public/rq.png');
        $qrStaticPath   = public_path('storage/qr.png');

        $data = [
            'solicitud'     => $solicitud,
            'logo_existe'   => file_exists($logoPath),
            'rq_existe'     => file_exists($rqDynamicPath),
            'fecha'         => now()->format('d/m/Y H:i'),
        ];

        if ($data['logo_existe']) {
            $data['logo'] = base64_encode(file_get_contents($logoPath));
        }
        if ($data['rq_existe']) {
            $data['rq'] = base64_encode(file_get_contents($rqDynamicPath));
        }
        if (file_exists($qrStaticPath)) {
            $data['qr_img_static'] = base64_encode(file_get_contents($qrStaticPath));
        }

        $pdf = Pdf::loadView('backend.pages.requests.documento_final', $data)
                  ->setPaper('letter','portrait')
                  ->setOption('isRemoteEnabled', true);

        return $pdf->download("debida_diligencia_{$id}_" . now()->format('YmdHis') . ".pdf");
    }

    // 10) Vista previa del PDF
    public function previewDocumento($id)
    {
        $this->authorize('admin.view');

        $solicitud      = Solicitud::findOrFail($id);
        $logoPath       = storage_path('app/public/logo.png');
        $rqDynamicPath  = storage_path('app/public/rq.png');
        $qrStaticPath   = public_path('storage/qr.png');

        return view('backend.pages.requests.documento_final', [
            'solicitud'      => $solicitud,
            'logo_existe'    => file_exists($logoPath),
            'rq_existe'      => file_exists($rqDynamicPath),
            'fecha'          => now()->format('d/m/Y H:i'),
            'logo'           => file_exists($logoPath)      ? base64_encode(file_get_contents($logoPath))      : null,
            'rq'             => file_exists($rqDynamicPath) ? base64_encode(file_get_contents($rqDynamicPath)) : null,
            'qr_img_static'  => file_exists($qrStaticPath)  ? base64_encode(file_get_contents($qrStaticPath))  : null,
        ]);
    }
}
