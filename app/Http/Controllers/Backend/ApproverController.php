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
    public function index(): Renderable
    {
        $this->checkAuthorization(Auth::user(), ['admin.view']);
        // Bandeja SAGRILAFT: estado APROBADOR_SAGRILAFT
        $solicitudes = Solicitud::where('estado', 'APROBADOR_SAGRILAFT')->get();

        return view('backend.pages.approve.index', [
            'solicitudes' => $solicitudes,
            'vista'       => 1,
        ]);
    }

    public function index2(): Renderable
    {
        $this->checkAuthorization(Auth::user(), ['admin.view']);
        // Bandeja PTEE: estado APROBADOR_PTEE
        $solicitudes = Solicitud::where('estado', 'APROBADOR_PTEE')->get();

        return view('backend.pages.approve.index', [
            'solicitudes' => $solicitudes,
            'vista'       => 2,
        ]);
    }

    public function show(Request $request, $id): Renderable
    {
        $this->checkAuthorization(Auth::user(), ['admin.view']);
        $solicitud = Solicitud::with('miembros')->findOrFail($id);

        // Detecta si estamos en PTEE (cualquier ruta que empiece con admin.approver2)
        $vista = request()->routeIs('admin.approver2.*') ? 2 : 1;

        return view('backend.pages.approve.show', [
            'solicitud' => $solicitud,
            'vista'     => $vista,
        ]);
    }

    public function save(Request $request, $id): RedirectResponse
    {
        $this->checkAuthorization(Auth::user(), ['admin.view']);

        // Tomamos primero el input "vista", si no existe tomamos el query param o 1 por defecto
        $vista = (int) $request->input('vista', $request->query('vista', 1));
        Log::debug("ApproverController::save - Vista recibida", ['vista' => $vista]);

        // Inicializamos los valores por defecto
        $concepto_sagrilaft = 'FAVORABLE';
        $concepto_ptee      = 'FAVORABLE';
        $concepto           = 'FAVORABLE';
        $motivoRechazo      = null;

        // Procesamos los miembros: los borramos y volvemos a insertar
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
                // Si alguno es "no", marcamos rechazo
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

        // Establecemos el nuevo estado
        if ($vista === 1 && $concepto_sagrilaft === 'NO FAVORABLE') {
            // Si SAGRILAFT rechaza, termina el flujo directamente
            $estado   = 'ENTREGADO';
            $concepto = 'NO FAVORABLE';
        } elseif ($vista === 1) {
            // Flujo normal: pasa a PTEE
            $estado = 'APROBADOR_PTEE';
        } else {
            // Vista 2 (PTEE): siempre entrega
            $estado = 'ENTREGADO';
        }

        // Si alguno de los conceptos es NO FAVORABLE, el global también lo es
        if ($concepto_sagrilaft === 'NO FAVORABLE' || $concepto_ptee === 'NO FAVORABLE') {
            $concepto = 'NO FAVORABLE';
        }

        // Actualizamos la solicitud
        Solicitud::where('id', $id)->update([
            'motivo_rechazo'     => $motivoRechazo,
            'estado'             => $estado,
            'concepto_sagrilaft' => $concepto_sagrilaft,
            'concepto_ptee'      => $concepto_ptee,
            'concepto'           => $concepto,
        ]);

        // Recargamos la solicitud actualizada
        $solicitud = Solicitud::with('admin')->findOrFail($id);

        // Enviamos notificación por correo al creador si cambió de estado
        Mail::to($solicitud->admin->email)
            ->send(new SolicitudStatusChanged($solicitud));

        // Si quedó ENTREGADO, insertamos o actualizamos en "informacion"
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

        // Redirect según la vista
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
