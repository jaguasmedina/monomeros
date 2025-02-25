<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    public function index(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.view']);

        return view('backend.pages.dashboard.index', [
            'admins' => Admin::all(),
        ]);
    }


    public function create(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.create']);

        return view('backend.pages.informations.create', [
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

        if ($request->roles) {
            $admin->assignRole($request->roles);
        }

        session()->flash('success', __('Usuario ha sido creado.'));
        return redirect()->route('backend.pages.dashboard.index');
    }

    public function edit(int $id): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.edit']);

        $admin = Admin::findOrFail($id);
        return view('backend.pages.informations.edit', [
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

        session()->flash('success', 'Admin has been updated.');
        return back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['admin.delete']);

        $admin = Admin::findOrFail($id);
        $admin->delete();
        session()->flash('success', 'Admin has been deleted.');
        return back();
    }
}
