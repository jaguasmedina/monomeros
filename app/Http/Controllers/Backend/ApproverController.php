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
use App\Models\Miembro;

class ApproverController extends Controller
{
    public function index(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.view']);
        $solicitudes = Solicitud::where('estado', 'aprobador')->get();
        return view('backend.pages.approve.index',[
            'solicitudes'=> $solicitudes,
            'vista' => 1
        ]);
    }
    public function show($id)
    {
        $solicitud = Solicitud::with('miembros')->findOrFail($id);
        return view('backend.pages.approve.show', [
            'solicitud' => $solicitud
        ]);
    }
    public function index2(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.view']);
        $solicitudes = Solicitud::where('estado', 'aprobador2')->get();
        return view('backend.pages.approve.index',[
            'solicitudes'=> $solicitudes,
            'vista' => 2
        ]);
    }
    public function save(Request $request, $id){
        $vista = $request->query('vista');
       // dd($vista, $id, $request->concepto_no_favorable,$request->miembros);

        $actualizarSolicitud = false;
        $motivoRechazo = null;
        $estado = 'documentacion';

        Miembro::where('solicitud_id', $id)->delete();
        foreach ($request->miembros as $miembroData) {
            Miembro::create([
                'solicitud_id' => $id,
                'titulo' => $miembroData['titulo'],
                'nombre' => $miembroData['nombre'],
                'tipo_id' => $miembroData['tipo_id'],
                'numero_id' => $miembroData['numero_id'],
                'favorable' => $miembroData['favorable'],
                'concepto_no_favorable' => $request->concepto_no_favorable
            ]);
        }
        if ($miembroData['favorable'] === "no") {
            $actualizarSolicitud = true;
            $motivoRechazo = $request->concepto_no_favorable ?? "No especificado";
            $estado = 'documentacion';
        }
        if($vista == 1 && $actualizarSolicitud == false){
            $estado= 'aprobador2';
        }
        if($vista == 2  && $actualizarSolicitud == false){
            $estado= 'revisado';
        }
        if ($actualizarSolicitud) {
            Solicitud::where('id', $id)->update([
                'motivo' => $motivoRechazo,
                'estado' => $estado
            ]);
        }else{
            Solicitud::where('id', $id)->update([
                'estado' => $estado
            ]);
        }

       return redirect()->back()->with('success', 'Se guardaron los cambios realizado');
    }
}
