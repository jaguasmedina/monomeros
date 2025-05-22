<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Solicitud;
use App\Models\Miembro;
use Carbon\Carbon;
use App\Mail\SolicitudStatusChanged;

class ApproverController extends Controller
{
    public function __construct()
    {
        // Aplica autenticación y autorización para todos los métodos
        $this->middleware('auth:admin');
        $this->middleware(function ($request, $next) {
            $this->checkAuthorization(Auth::user(), ['admin.view']);
            return $next($request);
        });
    }

    /**
     * Bandeja SAGRILAFT (estado = APROBADOR_SAGRILAFT)
     */
    public function index(): Renderable
    {
        $solicitudes = Solicitud::where('estado', 'APROBADOR_SAGRILAFT')->get();
        return view('backend.pages.approve.index', [
            'solicitudes' => $solicitudes,
            'vista'       => 1,
        ]);
    }

    /**
     * Bandeja PTEE (estado = APROBADOR_PTEE)
     */
    public function index2(): Renderable
    {
        $solicitudes = Solicitud::where('estado', 'APROBADOR_PTEE')->get();
        return view('backend.pages.approve.index', [
            'solicitudes' => $solicitudes,
            'vista'       => 2,
        ]);
    }

    /**
     * Mostrar formulario de revisión, detectando la vista (1=SAGRILAFT, 2=PTEE)
     */
    public function show(Request $request, $id): Renderable
    {
        $solicitud = Solicitud::with('miembros')->findOrFail($id);

        // Detecta si la ruta es approver2.* para saber si es PTEE
        $vista = request()->routeIs('admin.approver2.*') ? 2 : 1;

        return view('backend.pages.approve.show', [
            'solicitud' => $solicitud,
            'vista'     => $vista,
        ]);
    }

    /**
     * Procesar la decisión del revisor y avanzar estado, guardar conceptos,
     * enviar notificación al creador, e insertar en tabla “informacion” si aplica.
     */
    public function save(Request $request, $id): RedirectResponse
    {
        // ¿Quién nos llamó? 1=SAGRILAFT, 2=PTEE
        $vista = (int) $request->input('vista', $request->query('vista', 1));
        Log::debug("ApproverController::save - Vista recibida", ['vista' => $vista]);

        // Valores por defecto
        $concepto_sagrilaft = 'FAVORABLE';
        $concepto_ptee      = 'FAVORABLE';
        $concepto           = 'FAVORABLE';
        $motivoRechazo      = null;

        // Borrar e insertar miembros según el formulario
        if ($request->has('miembros') && is_array($request->miembros)) {
            Miembro::where('solicitud_id', $id)->delete();
            foreach ($request->miembros as $miembroData) {
                Miembro::create([
                    'solicitud_id'          => $id,
                    'titulo'                => $miembroData['titulo'],
                    'nombre'                => $miembroData['nombre'],
                    'tipo_id'               => $miembroData['tipo_id'],
                    'numero_id'             => $miembroData['numero_id'],
                    'favorable'             => $miembroData['favorable'],
                    'concepto_no_favorable' => $request->concepto_no_favorable,
                ]);
                if ($miembroData['favorable'] === 'no') {
                    $motivoRechazo = $request->concepto_no_favorable ?? 'No especificado';
                    if ($vista === 1) {
                        $concepto_sagrilaft = 'NO FAVORABLE';
                    } else {
                        $concepto_ptee = 'NO FAVORABLE';
                    }
                }
            }
        }

        // Determinar nuevo estado según flujo y conceptos
        if ($vista === 1 && $concepto_sagrilaft === 'NO FAVORABLE') {
            // Si SAGRILAFT rechaza, se entrega de inmediato
            $estado   = 'ENTREGADO';
            $concepto = 'NO FAVORABLE';
        } elseif ($vista === 1) {
            // Si SAGRILAFT aprueba, pasa a PTEE
            $estado = 'APROBADOR_PTEE';
        } else {
            // En PTEE siempre se entrega
            $estado = 'ENTREGADO';
        }

        // Concepto global: si alguno es NO FAVORABLE, el global también
        if ($concepto_sagrilaft === 'NO FAVORABLE' || $concepto_ptee === 'NO FAVORABLE') {
            $concepto = 'NO FAVORABLE';
        }

        // Actualizar la solicitud en BD
        Solicitud::where('id', $id)->update([
            'motivo_rechazo'     => $motivoRechazo,
            'estado'             => $estado,
            'concepto_sagrilaft' => $concepto_sagrilaft,
            'concepto_ptee'      => $concepto_ptee,
            'concepto'           => $concepto,
        ]);

        // Recargar solicitud con relación al admin creador
        $solicitud = Solicitud::with('admin')->findOrFail($id);

        // Enviar notificación por correo al usuario creador si cambió de estado
        try {
            Mail::to($solicitud->admin->email)
                ->send(new SolicitudStatusChanged($solicitud));
        } catch (\Exception $e) {
            Log::error("Error enviando correo de notificación: {$e->getMessage()}");
        }

        // Si quedó ENTREGADO, actualizar o insertar en tabla "informacion"
        if ($estado === 'ENTREGADO') {
            DB::table('informacion')->updateOrInsert(
                ['identificador' => $solicitud->identificador],
                [
                    'tipo'            => $solicitud->tipo_id,
                    'nombre_completo' => $solicitud->nombre_completo ?? $solicitud->razon_social,
                    'empresa'         => $solicitud->razon_social,
                    'fecha_registro'  => $solicitud->fecha_registro,
                    'fecha_vigencia'  => Carbon::parse($solicitud->fecha_registro)
                                              ->addYears(2)
                                              ->format('Y-m-d'),
                    'cargo'           => $solicitud->tipo_cliente,
                    'estado'          => $solicitud->concepto,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]
            );
        }

        // Redireccionar a la bandeja correspondiente con mensaje de éxito
        if ($vista === 2) {
            return redirect()
                ->route('admin.approver2.index')
                ->with('success', 'Decisión registrada. Volviendo a bandeja PTEE.');
        }

        return redirect()
            ->route('admin.approver.index')
            ->with('success', 'Decisión registrada. Volviendo a bandeja SAGRILAFT.');
    }
}
