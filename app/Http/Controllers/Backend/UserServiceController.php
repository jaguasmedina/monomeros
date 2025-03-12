<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class UserServiceController extends Controller
{
    use LogsActivity;

    public function request(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.view']);
        return view('backend.pages.requests.request');
    }

    public function query(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.view']);
        return view('backend.pages.dashboard.index', [
            'admins' => Admin::all(),
            'informations' => Information::all(),
        ]);
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
    public function store(){
        $this->checkAuthorization(auth()->user(), ['admin.create']);

       // $usuario = Information::create($request->all());
/*
        activity()
        ->useLogName('Information')
            ->causedBy(auth()->user())
            ->performedOn($usuario)
            ->withProperties(['attributes' => $request->all()])
            ->log('insertó un nuevo usuario');

        session()->flash('success', __('Usuario ha sido creado.'));*/
        return redirect()->route('admin.dashboard');
    }

}
