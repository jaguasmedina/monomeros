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

class UserServiceController extends Controller
{
    use LogsActivity;

    public function request(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.view']);
        return view('backend.pages.requests.request');
    }

    public function query(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.view']);
        return view('backend.pages.requests.query');
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
    public function store(Request $request){
        $request->validate([
            'tipo_persona'   => 'required|in:natural,juridica',
            'fecha_registro' => 'required|date',
            'razon_social'   => 'required|string|max:255',
            'tipo_id'        => 'required|string|max:10',
            'identificador'  => 'required|string|max:50',
            'motivo'         => 'required|string',
            'nombre_completo'=> 'nullable|string|max:255',
            'archivo'        => 'nullable|file|mimes:pdf|max:2048',
            'tipo_cliente'   => 'nullable|string|in:proveedor,cliente,visitante,contratista',
        ]);

        $archivoPath = null;
        if ($request->hasFile('archivo')) {
            $archivoPath = $request->file('archivo')->store('solicitudes', 'public');
        }

        $solicitud = Solicitud::create([
            'tipo_persona'   => $request->tipo_persona,
            'fecha_registro' => $request->fecha_registro,
            'razon_social'   => strtoupper($request->razon_social),
            'tipo_id'        => $request->tipo_id,
            'identificador'  => strtoupper($request->identificador),
            'motivo'         => strtoupper($request->motivo),
            'nombre_completo'=> strtoupper($request->nombre_completo ?? ''),
            'archivo'        => $archivoPath,
            'tipo_cliente'   => $request->tipo_cliente,
        ]);
        $solicitudId = $solicitud->id;

        return redirect()->route('admin.service.request')
                ->with('success', 'Solicitud creada exitosamente')
                ->with('solicitud_id', $solicitud->id);
    }

    public function update(Request $request)
{
    $request->validate([
        'id'             => 'required|exists:solicitudes,id',
        'tipo_persona'   => 'required|in:natural,juridica',
        'fecha_registro' => 'required|date',
        'razon_social'   => 'required|string|max:255',
        'tipo_id'        => 'required|string|max:10',
        'identificador'  => 'required|string|max:50',
        'motivo'         => 'required|string',
        'nombre_completo'=> 'nullable|string|max:255',
        'archivo'        => 'nullable|file|mimes:pdf|max:2048',
        'tipo_cliente'   => 'nullable|string|in:proveedor,cliente,visitante,contratista',
    ]);

    // Buscar la solicitud por ID
    $solicitud = Solicitud::findOrFail($request->id);

    // Manejo del archivo (si se sube uno nuevo, se reemplaza el anterior)
    if ($request->hasFile('archivo')) {
        // Eliminar archivo anterior si existe
        if ($solicitud->archivo) {
            Storage::disk('public')->delete($solicitud->archivo);
        }
        $archivoPath = $request->file('archivo')->store('solicitudes', 'public');
        $solicitud->archivo = $archivoPath;
    }

    // Actualizar los demás campos
    $solicitud->update([
        'tipo_persona'   => $request->tipo_persona,
        'fecha_registro' => $request->fecha_registro,
        'razon_social'   => strtoupper($request->razon_social),
        'tipo_id'        => $request->tipo_id,
        'identificador'  => strtoupper($request->identificador),
        'motivo'         => strtoupper($request->motivo),
        'nombre_completo'=> strtoupper($request->nombre_completo ?? ''),
        'tipo_cliente'   => $request->tipo_cliente,
        'estado'        => 'enviado',
    ]);
    return redirect()->route('admin.service.query')
        ->with('success', 'Solicitud actualizada exitosamente')
        ->with('solicitud_id', $solicitud->id);
}

    public function queryreq(Request $request ){
        $this->checkAuthorization(auth()->user(), ['admin.create']);
        $request->validate([
            'numero_solicitud' => 'nullable|integer',
            'identificador' => 'nullable|string|max:50',
        ]);
        $solicitud = Solicitud::when($request->numero_solicitud, function ($query, $numero_solicitud) {
            return $query->where('id', $numero_solicitud);
        })
        ->when($request->identificador, function ($query, $identificador) {
            return $query->where('identificador', $identificador);
        })
        ->get();

        return view('backend.pages.requests.query', [
            'solicitud' => $solicitud,
            'numero_solicitud' => $request->numero_solicitud,
            'identificador' => $request->identificador
        ]);

    }
    public function edit(Request $request, $id){
        $this->checkAuthorization(auth()->user(), ['admin.view']);
        $solicitud = Solicitud::findOrFail($id);
        return view('backend.pages.requests.edit', [
            'solicitud' => $solicitud,
        ]);
    }

}
