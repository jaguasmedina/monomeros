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
use Carbon\Carbon;

use App\Models\Solicitud;
use App\Models\Miembro;
use App\Models\MovimientoSolicitud;
use App\Mail\SolicitudStatusChanged;

class ApproverController extends Controller
{
    public function __construct()
    {
        // Si utilizas un m�todo de autorizaci�n personalizado, act�valo aqu�
        // $this->middleware('can:admin.view');
    }

    /**
     * Bandeja de SAGRILAFT: solicitudes en estado APROBADOR_SAGRILAFT
     */
    public function index(): Renderable
    {
        $this->checkAuthorization(Auth::user(), ['admin.view']);
        $solicitudes = Solicitud::where('estado', 'APROBADOR_SAGRILAFT')->get();

        return view('backend.pages.approve.index', [
            'solicitudes' => $solicitudes,
            'vista'       => 1,
        ]);
    }

    /**
     * Bandeja de PTEE: solicitudes en estado APROBADOR_PTEE
     */
    public function index2(): Renderable
    {
        $this->checkAuthorization(Auth::user(), ['admin.view']);
        $solicitudes = Solicitud::where('estado', 'APROBADOR_PTEE')->get();

        return view('backend.pages.approve.index', [
            'solicitudes' => $solicitudes,
            'vista'       => 2,
        ]);
    }

    /**
     * Mostrar detalle de solicitud para revisi�n (SAGRILAFT o PTEE)
     */
    public function show(Request $request, $id): Renderable
    {
        $this->checkAuthorization(Auth::user(), ['admin.view']);
        $solicitud = Solicitud::with('miembros')->findOrFail($id);

        // Determinar si es ruta PTEE (vista=2) o SAGRILAFT (vista=1)
        $vista = $request->routeIs('admin.approver2.*') ? 2 : 1;

        return view('backend.pages.approve.show', [
            'solicitud' => $solicitud,
            'vista'     => $vista,
        ]);
    }

    /**
     * Guardar decisi�n de SAGRILAFT o PTEE:
     * - Inserta/Actualiza miembros
     * - Actualiza estado y concepto en solicitudes
     * - Registra movimiento en movimientos_solicitudes
     * - Notifica por correo (comentado temporalmente)
     * - Si ENTREGADO, inserta/actualiza en tabla �informacion�
     */
    public function save(Request $request, $id): RedirectResponse
    {
        $this->checkAuthorization(Auth::user(), ['admin.view']);

        // 1 = SAGRILAFT, 2 = PTEE
        $vista = (int) $request->input('vista', $request->query('vista', 1));
        Log::debug("ApproverController::save - Vista recibida", ['vista' => $vista]);

        // Cargar la solicitud original para obtener estadoAnterior
        $solOriginal    = Solicitud::findOrFail($id);
        $estadoAnterior = $solOriginal->estado;

        // Conceptos por defecto
        $concepto_sagrilaft = 'FAVORABLE';
        $concepto_ptee      = 'FAVORABLE';
        $concepto           = 'FAVORABLE';
        $motivoRechazo      = null;

        // 2) Reemplazo de miembros si vienen en el request
        if ($request->has('miembros') && is_array($request->miembros)) {
            Miembro::where('solicitud_id', $id)->delete();
            foreach ($request->miembros as $miData) {
                Miembro::create([
                    'solicitud_id'          => $id,
                    'titulo'                => $miData['titulo'],
                    'nombre'                => $miData['nombre'],
                    'tipo_id'               => $miData['tipo_id'],
                    'numero_id'             => $miData['numero_id'],
                    'favorable'             => $miData['favorable'],
                    'concepto_no_favorable' => $request->concepto_no_favorable,
                ]);

                if ($miData['favorable'] === 'no') {
                    $motivoRechazo = $request->concepto_no_favorable ?? 'No especificado';
                    if ($vista === 1) {
                        $concepto_sagrilaft = 'NO FAVORABLE';
                    } else {
                        $concepto_ptee = 'NO FAVORABLE';
                    }
                }
            }
        }

        // 3) Determinar nuevo estado y concepto global
        if ($vista === 1 && $concepto_sagrilaft === 'NO FAVORABLE') {
            // Si SAGRILAFT rechaza, marca como ENTREGADO
            $estado   = 'ENTREGADO';
            $concepto = 'NO FAVORABLE';
        } elseif ($vista === 1) {
            // Si SAGRILAFT aprueba, pasa a PTEE
            $estado = 'APROBADOR_PTEE';
        } else {
            // En PTEE siempre entrega (ENTREGADO)
            $estado = 'ENTREGADO';
        }

        // Si alguno dict� NO FAVORABLE, concepto global = NO FAVORABLE
        if ($concepto_sagrilaft === 'NO FAVORABLE' || $concepto_ptee === 'NO FAVORABLE') {
            $concepto = 'NO FAVORABLE';
        }

        // 4) Actualizar la solicitud
        Solicitud::where('id', $id)->update([
            'motivo_rechazo'     => $motivoRechazo,
            'estado'             => $estado,
            'concepto_sagrilaft' => $concepto_sagrilaft,
            'concepto_ptee'      => $concepto_ptee,
            'concepto'           => $concepto,
        ]);

        // 5) Registrar movimiento en historial
        MovimientoSolicitud::create([
            'solicitud_id'    => $id,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo'    => $estado,
            'comentario'      => $vista === 1
                                    ? 'Revisi&oacute;n SAGRILAFT: ' . $concepto_sagrilaft
                                    : 'Revisi&oacute;n PTEE: ' . $concepto_ptee,
            'fecha_movimiento'=> now(),
        ]);

        // 6) Notificar por correo al creador si cambió el estado a ENTREGADO
            $solicitud = Solicitud::with('admin')->findOrFail($id);
            if ($estado === 'ENTREGADO') {
            Mail::to($solicitud->admin->email)
            ->send(new SolicitudStatusChanged($solicitud));
            }


        // 7) Si ENTREGADO, insertar/actualizar en tabla �informacion�
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

        // 8) Redireccionar seg�n la vista
        if ($vista === 2) {
            return redirect()
                ->route('admin.approver2.index')
                 ->with('success', "Decisi\u{00F3}n registrada. Volviendo a PTEE.");
        }

        return redirect()
            ->route('admin.approver.index')
            ->with('success', "Decisi\u{00F3}n registrada. Volviendo a SAGRILAFT.");
    }
}