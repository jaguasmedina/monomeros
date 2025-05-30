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
use App\Models\MovimientoSolicitud;     // ¡Importa el modelo de movimientos!
use App\Mail\SolicitudStatusChanged;    // Tu Mailable para notificar cambios

class ApproverController extends Controller
{
    public function index(): Renderable
    {
        $this->checkAuthorization(Auth::user(), ['admin.view']);
        $solicitudes = Solicitud::where('estado', 'APROBADOR_SAGRILAFT')->get();

        return view('backend.pages.approve.index', [
            'solicitudes' => $solicitudes,
            'vista'       => 1,
        ]);
    }

    public function index2(): Renderable
    {
        $this->checkAuthorization(Auth::user(), ['admin.view']);
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

        // Detecta si es la ruta 2 (PTEE) o 1 (SAGRILAFT)
        $vista = $request->routeIs('admin.approver2.*') ? 2 : 1;

        return view('backend.pages.approve.show', [
            'solicitud' => $solicitud,
            'vista'     => $vista,
        ]);
    }


    public function save(Request $request, $id): RedirectResponse
    {
        $this->checkAuthorization(Auth::user(), ['admin.view']);
    
        $vista = (int) $request->input('vista', $request->query('vista', 1));
        Log::debug("ApproverController::save - Vista recibida", ['vista' => $vista]);
    
        $solOriginal = Solicitud::findOrFail($id);
        $estadoAnterior = $solOriginal->estado;
    
        $concepto_sagrilaft = 'FAVORABLE';
        $concepto_ptee      = 'FAVORABLE';
        $concepto           = 'FAVORABLE';
        $motivoRechazo      = null;
    
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
    
        if ($vista === 1 && $concepto_sagrilaft === 'NO FAVORABLE') {
            $estado = 'ENTREGADO';
            $concepto = 'NO FAVORABLE';
        } elseif ($vista === 1) {
            $estado = 'APROBADOR_PTEE';
        } else {
            $estado = 'ENTREGADO';
        }
    
        if ($concepto_sagrilaft === 'NO FAVORABLE' || $concepto_ptee === 'NO FAVORABLE') {
            $concepto = 'NO FAVORABLE';
        }
    
        Solicitud::where('id', $id)->update([
            'motivo_rechazo'     => $motivoRechazo,
            'estado'             => $estado,
            'concepto_sagrilaft' => $concepto_sagrilaft,
            'concepto_ptee'      => $concepto_ptee,
            'concepto'           => $concepto,
        ]);
    
        MovimientoSolicitud::create([
            'solicitud_id'    => $id,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo'    => $estado,
            'comentario'      => $vista === 1
                                    ? 'Revisión SAGRILAFT: '.$concepto_sagrilaft
                                    : 'Revisión PTEE: '.$concepto_ptee,
            'fecha_movimiento'=> now(),
        ]);
    
        $solicitud = Solicitud::with('admin')->findOrFail($id);
    
        // Comentado temporalmente por errores de TLS en el envío
        // Mail::to($solicitud->admin->email)
        //     ->send(new SolicitudStatusChanged($solicitud));
    
        if ($estado === 'ENTREGADO') {
            DB::table('informacion')->updateOrInsert(
                ['identificador' => $solicitud->identificador],
                [
                    'tipo'            => $solicitud->tipo_id,
                    'nombre_completo' => $solicitud->nombre_completo ?? $solicitud->razon_social,
                    'empresa'         => $solicitud->razon_social,
                    'fecha_registro'  => $solicitud->fecha_registro,
                    'fecha_vigencia'  => Carbon::parse($solicitud->fecha_registro)->addYears(2)->format('Y-m-d'),
                    'cargo'           => $solicitud->tipo_cliente,
                    'estado'          => $solicitud->concepto,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]
            );
        }
    
        return redirect()
            ->route($vista === 2 ? 'admin.approver2.index' : 'admin.approver.index')
            ->with('success', 'Decisión registrada. Volviendo a ' . ($vista === 2 ? 'PTEE' : 'SAGRILAFT') . '.');
    }
    


}
