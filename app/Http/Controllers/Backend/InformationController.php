<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use App\Models\Information;
use App\Http\Requests\UserRequest;
use App\Imports\InformacionImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Admin;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;
use App\Exports\InformationExport;
use Illuminate\Support\Facades\DB;

class InformationController extends Controller
{
    use LogsActivity;

    public function index(): Renderable
{
    $this->checkAuthorization(Auth::user(), ['admin.view']);

    // Con eager loading para no hacer queries N+1
    $informations = Information::with('solicitud')->get();

    return view('backend.pages.dashboard.index', [
        'admins'      => Admin::all(),
        'informations'=> $informations,
    ]);
}

    public function report(Request $request): Renderable
    {
        // permisos
        $this->checkAuthorization(Auth::user(), ['admin.view']);

        // 1) Recoger filtros de la petición (por GET)
        $filters = $request->only(['fecha_inicio', 'fecha_fin', 'empresa', 'estado']);

        // 2) Construir la consulta uniendo con solicitudes para traer el ID
        $query = DB::table('informacion')
            ->join('solicitudes', 'informacion.identificador', '=', 'solicitudes.identificador')
            ->select(
                'solicitudes.id as solicitud_id',
                'informacion.identificador',
                'informacion.tipo',
                'informacion.nombre_completo',
                'informacion.empresa',
                'informacion.fecha_registro',
                'informacion.fecha_vigencia',
                'informacion.cargo',
                'informacion.estado'
            );

        // 3) Aplicar filtros
        if (!empty($filters['fecha_inicio'])) {
            $query->where('informacion.fecha_registro', '>=', $filters['fecha_inicio']);
        }
        if (!empty($filters['fecha_fin'])) {
            $query->where('informacion.fecha_registro', '<=', $filters['fecha_fin']);
        }
        if (!empty($filters['empresa'])) {
            $query->where('informacion.empresa', 'like', '%' . $filters['empresa'] . '%');
        }
        if (!empty($filters['estado'])) {
            $query->where('informacion.estado', $filters['estado']);
        }

        // 4) Ejecutar y pasar datos a la vista
        $informacion = $query->get();

        return view('backend.pages.reports.informacion', compact('informacion', 'filters'));
    }

    public function exportReport(Request $request)
    {
        $filters = $request->only(['fecha_inicio', 'fecha_fin', 'empresa', 'estado']);
        return Excel::download(new InformationExport($filters), 'informacion.xlsx');
    }

    public function create(): Renderable
    {
        $this->checkAuthorization(Auth::user(), ['admin.create']);
        return view('backend.pages.dashboard.create');
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $this->checkAuthorization(Auth::user(), ['admin.create']);
        $usuario = Information::create($request->all());
        activity()
            ->useLogName('Information')
            ->causedBy(Auth::user())
            ->performedOn($usuario)
            ->withProperties(['attributes' => $request->all()])
            ->log('insertó un nuevo usuario');
        session()->flash('success', __('Usuario ha sido creado.'));
        return redirect()->route('admin.dashboard');
    }

    public function edit(string $id): Renderable
    {
        $this->checkAuthorization(Auth::user(), ['admin.edit']);
        return view('backend.pages.dashboard.edit', [
            'usuario' => Information::findOrFail($id)
        ]);
    }

    public function update(UserRequest $request, $id): RedirectResponse
    {
        $this->checkAuthorization(Auth::user(), ['admin.edit']);
        $usuario = Information::findOrFail($id);
        $usuario->update($request->all());
        activity()
            ->useLogName('Information')
            ->causedBy(Auth::user())
            ->performedOn($usuario)
            ->withProperties(['attributes' => $request->all()])
            ->log('editó un usuario');
        session()->flash('success', 'Usuario ha sido actualizado.');
        return back();
    }

    public function destroy($id): RedirectResponse
    {
        $this->checkAuthorization(Auth::user(), ['admin.delete']);
        $usuario = Information::findOrFail($id);
        $usuario->delete();
        activity()
            ->useLogName('Information')
            ->causedBy(Auth::user())
            ->performedOn($usuario)
            ->log('eliminó un usuario');
        session()->flash('success', 'Usuario ha sido eliminado.');
        return back();
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv|max:2048']);
        Excel::import(new InformacionImport, $request->file('file'));
        activity()
            ->useLogName('Information')
            ->causedBy(Auth::user())
            ->log('importó datos desde un archivo');
        return redirect()->route('admin.informations.index')->with('success', 'Datos importados correctamente.');
    }

    public function uploadExcel()
    {
        return view('backend.pages.dashboard.upload_excel');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name','email','created_at','updated_at'])
            ->useLogName('Information')
            ->setDescriptionForEvent(fn(string $eventName) => "Se ha realizado la acción: {$eventName} en un usuario")
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
