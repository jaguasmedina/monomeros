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

class InformationController extends Controller
{
    use LogsActivity;

    public function index(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.view']);
        return view('backend.pages.dashboard.index', [
            'admins' => Admin::all(),
            'informations' => Information::all(),
        ]);
    }

    public function create(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.create']);
        return view('backend.pages.dashboard.create');
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['admin.create']);

        $usuario = Information::create($request->all());

        activity()
            ->causedBy(auth()->user())
            ->performedOn($usuario)
            ->withProperties(['attributes' => $request->all()])
            ->log('insertó un nuevo usuario');

        session()->flash('success', __('Usuario ha sido creado.'));
        return redirect()->route('admin.dashboard');
    }

    public function edit(string $id): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.edit']);
        return view('backend.pages.dashboard.edit', [
            'usuario' => Information::findOrFail($id)
        ]);
    }

    public function update(UserRequest $request, int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['admin.edit']);

        $usuario = Information::findOrFail($id);
        $usuario->update($request->all());

        activity()
            ->causedBy(auth()->user())
            ->performedOn($usuario)
            ->withProperties(['attributes' => $request->all()])
            ->log('editó un usuario');

        session()->flash('success', 'Usuario ha sido actualizado.');
        return back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['admin.delete']);

        $usuario = Information::findOrFail($id);
        $usuario->delete();

        activity()
            ->causedBy(auth()->user())
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
            ->causedBy(auth()->user())
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
            ->useLogName('information')
            ->setDescriptionForEvent(fn(string $eventName) => "Se ha realizado la acción: {$eventName} en un usuario")
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

}
