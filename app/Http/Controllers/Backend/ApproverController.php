<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\Solicitud;
use App\Models\Miembro;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ApproverController extends Controller
{
    public function index(): Renderable
    {
        $this->checkAuthorization(Auth::user(), ['admin.view']);
        $solicitudes = Solicitud::where('estado', 'aprobador_SAGRILAFT')->get();
        return view('backend.pages.approve.index', [
            'solicitudes' => $solicitudes,
            'vista' => 1
        ]);
    }

    public function show($id)
    {
        $solicitud = Solicitud::with('miembros')->findOrFail($id);
        return view('backend.pages.approve.show', [
            'solicitud' => $solicitud,
        ]);
    }

    public function index2(): Renderable
    {
        $this->checkAuthorization(Auth::user(), ['admin.view']);
        $solicitudes = Solicitud::where('estado', 'aprobador_PTEE')->get();
        return view('backend.pages.approve.index', [
            'solicitudes' => $solicitudes,
            'vista' => 2
        ]);
    }

    public function save(Request $request, $id)
    {
        // Convertir el parámetro "vista" a entero; se espera 1 (primer revisor) o 2 (segundo revisor)
        $vista = (int) $request->query('vista', 1);
        Log::debug("ApproverController::save - Vista recibida", ['vista' => $vista]);

        // Valores por defecto
        $concepto_sagrilaft = 'Favorable';
        $concepto_ptee = 'Favorable';
        $concepto = 'Favorable';
        $actualizarSolicitud = false;
        $motivoRechazo = null;
        // Valor inicial para el estado
        $estado = 'documentacion';

        if (!empty($request->miembros) && is_array($request->miembros)) {
            // Eliminamos los miembros previos
            Miembro::where('solicitud_id', $id)->delete();
            foreach ($request->miembros as $miembroData) {
                Miembro::create([
                    'solicitud_id' => $id,
                    'titulo'       => $miembroData['titulo'],
                    'nombre'       => $miembroData['nombre'],
                    'tipo_id'      => $miembroData['tipo_id'],
                    'numero_id'    => $miembroData['numero_id'],
                    'favorable'    => $miembroData['favorable'],
                    'concepto_no_favorable' => $request->concepto_no_favorable,
                ]);
            }
            // Si el favorable del último miembro es "no", marcamos que se debe actualizar la solicitud (rechazo)
            if ($miembroData['favorable'] === "no") {
                $actualizarSolicitud = true;
                $motivoRechazo = $request->concepto_no_favorable ?? "No especificado";
                if ($vista == 1) {
                    $estado = 'APROBADOR_PTEE';
                }
            }
        }

        if ($vista == 1) {
            // Para el primer revisor: se fija estado a 'APROBADOR_PTEE'
            $estado = 'APROBADOR_PTEE';
            if ($actualizarSolicitud) {
                $concepto_sagrilaft = 'NO FAVORABLE';
            }
        } elseif ($vista == 2) {
            // Para el segundo revisor (PTEE): forzamos el estado a 'ENTREGADO'
            $estado = 'ENTREGADO';
            if ($actualizarSolicitud) {
                $concepto_ptee = 'NO FAVORABLE';
            }
        }

        // Si alguno de los conceptos es "NO FAVORABLE", se asigna al concepto global
        if ($concepto_sagrilaft === 'NO FAVORABLE' || $concepto_ptee === 'NO FAVORABLE') {
            $concepto = 'NO FAVORABLE';
        }

        // Capturamos el estado anterior de la solicitud antes de actualizar
        $solicitudOriginal = Solicitud::findOrFail($id);
        $estadoAnterior = $solicitudOriginal->estado;
        Log::debug("Estado anterior:", ['estado' => $estadoAnterior]);

        // Actualizamos la solicitud
        Solicitud::where('id', $id)->update([
            'motivo_rechazo'     => $motivoRechazo,
            'estado'             => $estado,
            'concepto_sagrilaft' => $concepto_sagrilaft,
            'concepto_ptee'      => $concepto_ptee,
            'concepto'           => $concepto,
        ]);

        // Recargamos la solicitud actualizada
        $solicitud = Solicitud::findOrFail($id);
        Log::debug("Estado actualizado en solicitud", ['estado' => $solicitud->estado]);

        // Registrar el movimiento se realiza automáticamente en el Observer
        // (Se elimina la llamada manual para evitar duplicidad)

        // Si el estado es ENTREGADO, actualizamos o insertamos en la tabla "informacion"
        if (strtoupper($solicitud->estado) === 'ENTREGADO') {
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

        return redirect()->back()->with('success', 'Se guardaron los cambios realizados');
    }

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
