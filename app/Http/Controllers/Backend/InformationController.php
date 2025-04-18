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



class InformationController extends Controller
{
    use LogsActivity;



    public function index(): Renderable
    {
        $this->checkAuthorization(Auth::user(), ['admin.view']);
        return view('backend.pages.dashboard.index', [
            'admins' => Admin::all(),
            'informations' => Information::all(),
        ]);
    }

    public function report(Request $request): Renderable
{
    // Recoger filtros de la petición (por GET)
    $filters = $request->only(['fecha_inicio', 'fecha_fin', 'empresa', 'estado']);

    // Construir la consulta
    $query = \App\Models\Information::query();
    if (!empty($filters['fecha_inicio'])) {
        $query->where('fecha_registro', '>=', $filters['fecha_inicio']);
    }
    if (!empty($filters['fecha_fin'])) {
        $query->where('fecha_registro', '<=', $filters['fecha_fin']);
    }
    if (!empty($filters['empresa'])) {
        $query->where('empresa', 'like', '%' . $filters['empresa'] . '%');
    }
    if (!empty($filters['estado'])) {
        $query->where('estado', $filters['estado']);
    }
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
        ->useLog('Information')
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
        ->useLog('Information')
            ->causedBy(Auth::user())
            ->performedOn($usuario)
            ->log('eliminó un usuario');

        session()->flash('success', 'Usuario ha sido eliminado.');
        return back();
    }

    public function import(Request $request)
    {

        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:2048'
        ]);

        Excel::import(new InformacionImport, $request->file('file'));

        activity()
        ->useLog ('Information')
            ->causedBy(Auth::user())
            ->log('importó datos desde un archivo');

        return redirect()->route('admin.informations.index')->with('success', 'Datos importados correctamente.');

    }

    public function uploadExcel()
    {
        return view('backend.admin.upload_excel');
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

}
