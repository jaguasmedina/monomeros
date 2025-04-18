<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\Solicitud;
use App\Models\Miembro;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
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

    // Muestra la vista para crear una nueva solicitud
    public function request(): Renderable
    {
        $this->authorize('admin.view');
        return view('backend.pages.requests.request');
    }

    // Método report: genera el reporte de solicitudes filtrado
    public function report(Request $request): Renderable
    {
        $filters = $request->only(['fecha_inicio', 'fecha_fin', 'razon_social', 'estado']);
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

        return view('backend.pages.reports.solicitudes', compact('solicitudes', 'filters'));
    }

    // Exporta el reporte de solicitudes a Excel
    public function exportReport(Request $request)
    {
        $filters = $request->only(['fecha_inicio', 'fecha_fin', 'razon_social', 'estado']);
        return Excel::download(new SolicitudesExport($filters), 'solicitudes.xlsx');
    }

    // Procesa la consulta de solicitudes (vista Query)
    public function handleQuery(Request $request): Renderable
    {
        $this->authorize('admin.view');

        if ($request->isMethod('post')) {
            $request->validate([
                'numero_solicitud' => 'nullable|integer',
                'identificador'    => 'nullable|string|max:50',
            ]);

            $solicitud = Solicitud::when($request->numero_solicitud, function ($query, $numero_solicitud) {
                    return $query->where('id', $numero_solicitud);
                })
                ->when($request->identificador, function ($query, $identificador) {
                    return $query->where('identificador', strtoupper($identificador));
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

    // Procesa la creación de una nueva solicitud
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'tipo_persona'    => 'required|in:natural,juridica',
            'fecha_registro'  => 'required|date',
            'razon_social'    => 'required|string|max:255',
            'tipo_id'         => 'required|string|max:10',
            'identificador'   => 'required|string|max:50',
            'motivo'          => 'required|string',
            'nombre_completo' => $request->tipo_persona === 'natural' ? 'required|string|max:255' : 'nullable',
            'archivos'        => $request->tipo_persona === 'juridica' ? 'required|array|min:1|max:3' : 'nullable',
            'archivos.*'      => 'file|mimes:pdf|max:2048',
            'tipo_cliente'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $archivosPaths = [];
        if ($request->tipo_persona === 'juridica' && $request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                $path = $archivo->store('solicitudes/' . date('Y/m'), 'public');
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

    // Muestra el formulario de edición de una solicitud
    public function edit($id): Renderable
    {
        $this->authorize('admin.edit');
        $solicitud = Solicitud::findOrFail($id);
        return view('backend.pages.requests.edit', compact('solicitud'));
    }

    // Muestra las solicitudes creadas por el usuario autenticado (rol "usuarios")
    public function misSolicitudes(Request $request): Renderable
    {
        $adminId = Auth::guard('admin')->id();

        $solicitudes = Solicitud::where('admin_id', $adminId)
            ->with(['admin' => function($query) {
                $query->select('id', 'name');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        Log::debug('Solicitudes del usuario', [
            'admin_id' => $adminId,
            'count' => $solicitudes->count(),
            'solicitudes' => $solicitudes->pluck('id')
        ]);

        return view('backend.pages.solicitudes.mis_solicitudes', compact('solicitudes'));
    }

    // Actualiza una solicitud (en el flujo de edición)
    public function update(Request $request): RedirectResponse
    {
        $this->authorize('admin.edit');

        $request->validate([
            'id'              => 'required|exists:solicitudes,id',
            'tipo_persona'    => 'required|in:natural,juridica',
            'fecha_registro'  => 'required|date',
            'razon_social'    => 'required|string|max:255',
            'tipo_id'         => 'required|string|max:10',
            'identificador'   => 'required|string|max:50',
            'motivo'          => 'required|string',
            'nombre_completo' => 'nullable|string|max:255',
            'archivo'         => 'nullable|file|mimes:pdf|max:2048',
            'tipo_cliente'    => 'nullable|string|in:proveedor,cliente,visitante,contratista',
        ]);

        $solicitud = Solicitud::findOrFail($request->id);

        if ($request->hasFile('archivo')) {
            if ($solicitud->archivo) {
                Storage::disk('public')->delete($solicitud->archivo);
            }
            $solicitud->archivo = $request->file('archivo')->store('solicitudes', 'public');
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

    // Genera el documento PDF final de la solicitud
    public function generarDocumentoFinal($id)
    {
        $this->authorize('admin.view');

        try {
            $solicitud = Solicitud::findOrFail($id);
            $logoPath = storage_path('app/public/logo.png');
            $rqPath = storage_path('app/public/rq.png');

            $data = [
                'solicitud'   => $solicitud,
                'logo_existe' => file_exists($logoPath),
                'rq_existe'   => file_exists($rqPath),
                'fecha'       => now()->format('d/m/Y H:i'),
            ];

            if ($data['logo_existe']) {
                $data['logo'] = base64_encode(file_get_contents($logoPath));
            }
            if ($data['rq_existe']) {
                $data['rq'] = base64_encode(file_get_contents($rqPath));
            }

            $pdf = Pdf::loadView('backend.pages.requests.documento_final', $data);
            $pdf->setPaper('letter', 'portrait');
            $pdf->setOption('isRemoteEnabled', true);

            Log::info("PDF generado para solicitud ID: {$id}", [
                'usuario' => Auth::guard('admin')->id(),
                'ip'      => request()->ip(),
            ]);

            return $pdf->download("debida_diligencia_{$id}_" . now()->format('YmdHis') . ".pdf");
        } catch (\Exception $e) {
            Log::error("Error generando PDF: " . $e->getMessage(), [
                'solicitud_id' => $id,
                'error'        => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Error al generar el documento: ' . $e->getMessage());
        }
    }

    // Previsualiza el documento PDF
    public function previewDocumento($id)
    {
        $this->authorize('admin.view');
        $solicitud = Solicitud::findOrFail($id);
        $logoPath = storage_path('app/public/logo.png');
        $rqPath = storage_path('app/public/rq.png');
        $data = [
            'solicitud'   => $solicitud,
            'logo_existe' => file_exists($logoPath),
            'rq_existe'   => file_exists($rqPath),
            'fecha'       => now()->format('d/m/Y H:i'),
        ];
        if ($data['logo_existe']) {
            $data['logo'] = base64_encode(file_get_contents($logoPath));
        }
        if ($data['rq_existe']) {
            $data['rq'] = base64_encode(file_get_contents($rqPath));
        }
        return view('backend.pages.requests.documento_final', $data);
    }
}
