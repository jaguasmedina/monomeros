<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Models\Admin;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\LogOptions;

class AdminsController extends Controller
{
    use LogsActivity;

    public function index(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.view']);

        return view('backend.pages.admins.index', [
            'admins' => Admin::all(),
        ]);
    }

    public function create(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.create']);

        return view('backend.pages.admins.create', [
            'roles' => Role::all(),
        ]);
    }

    public function store(AdminRequest $request): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['admin.create']);

        $admin = new Admin();
        $admin->name = $request->name;
        $admin->username = $request->username;
        $admin->email = $request->email;
        $admin->password = Hash::make($request->password);
        $admin->save();

        activity()
        ->useLogName('administrador')
        ->causedBy(auth()->user())
        ->performedOn($admin)
        ->log("Usuario {$admin->name} ha sido creado");

        if ($request->roles) {
            $admin->assignRole($request->roles);
        }

        session()->flash('success', __('AAdministrador ha sido creado.'));
        return redirect()->route('admin.admins.index');
    }

    public function edit(int $id): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.edit']);

        $admin = Admin::findOrFail($id);
        return view('backend.pages.admins.edit', [
            'admin' => $admin,
            'roles' => Role::all(),
        ]);
    }

    public function update(AdminRequest $request, int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['admin.edit']);

        $admin = Admin::findOrFail($id);
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->username = $request->username;
        if ($request->password) {
            $admin->password = Hash::make($request->password);
        }
        $admin->save();

        $admin->roles()->detach();
        if ($request->roles) {
            $admin->assignRole($request->roles);
        }
        activity()
        ->useLogName('administrador')
        ->causedBy(auth()->user())
        ->performedOn($admin)
        ->log("Usuario {$admin->name} ha sido actualizado");

        session()->flash('success', 'Administrador ha sido actualizado.');
        return back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['admin.delete']);

        $admin = Admin::findOrFail($id);
        $admin->delete();
        activity()
        ->useLogName('administrador')
        ->causedBy(auth()->user())
        ->performedOn($admin)
        ->log("Usuario {$admin->name} ha sido eliminado");
        session()->flash('success', 'Administrador Ha sido eliminado.');
        return back();
    }

    public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->logOnly(['name', 'username', 'email'])
        ->useLogName('administrador')
        ->setDescriptionForEvent(fn(string $eventName) => "Se ha realizado la acción: {$eventName} en un administrador")
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs();
}


}
