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

class AnalistsController extends Controller
{
    public function index(): Renderable
    {   $this->checkAuthorization(auth()->user(), ['admin.view']);
        $solicitudes = Solicitud::where('estado', 'enviado')->get();
        return view('backend.pages.analists.index', [
            'solicitudes' => $solicitudes,
        ]);
    }
    public function show($id)
{
    $solicitud = Solicitud::findOrFail($id);
    return view('backend.pages.analists.show',[
        'solicitud' => $solicitud,
    ]);
}
}
