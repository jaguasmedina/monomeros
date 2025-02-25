<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use App\Models\information;
use App\Http\Requests\UserRequest;
use App\Imports\InformacionImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Admin;
use Carbon\Carbon;

class InformationController extends Controller
{
    public function index(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.view']);

        return view('backend.pages.dashboard.index', [
            'admins' => Admin::all(),
            'informations' => information::all(),
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

        $usuario = new information();
        $usuario->identificador = $request->identificador;
        $usuario->tipo = $request->tipo;
        $usuario->nombre_completo = $request->nombre_completo;
        $usuario->empresa = $request->empresa;
        $usuario->fecha_registro = $request->fecha_registro;
        $usuario->fecha_vigencia = $request->fecha_vigencia;
        $usuario->cargo = $request->cargo;
        $usuario->estado = $request->estado;
        $usuario->save();
        session()->flash('success', __('Usuario ha sido creado.'));
        return redirect()->route('admin.dashboard',[]);
    }

    public function edit(string $id): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.edit']);

        $usuario = information::where('identificador', $id)->first();

        return view('backend.pages.dashboard.edit', [
            'usuario' => $usuario
        ]);
    }

    public function update(UserRequest $request, int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['admin.edit']);

        $usuario = information::where('identificador',$id)->first();

        $usuario->tipo = $request->tipo ?? $usuario->tipo;
        $usuario->nombre_completo = $request->nombre_completo ?? $usuario->nombre_completo;
        $usuario->empresa = $request->empresa ?? $usuario->empresa;
        $usuario->fecha_registro = $request->fecha_registro ?? $usuario->fecha_registro;
        $usuario->fecha_vigencia = $request->fecha_vigencia ?? $usuario->fecha_vigencia;
        $usuario->cargo = $request->cargo ?? $usuario->cargo;
        $usuario->estado = $request->estado ?? $usuario->estado;

        $usuario->save();

        session()->flash('success', 'Usuario ha sido actualizado.');
        return back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['admin.delete']);

        $admin = information::findOrFail($id);
        $admin->delete();
        session()->flash('success', 'Usuaria ha sido eliminado');
        return back();
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:2048'
        ]);

        Excel::import(new InformacionImport, $request->file('file'));

        return redirect()->route('admin.informations.index')->with('success', 'Datos importados correctamente.');
    }

            public function uploadExcel()
        {
            return view('backend.admin.upload_excel');
        }
}
