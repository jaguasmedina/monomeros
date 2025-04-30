<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Information;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('admin')->user();

        // Si el rol es "usuarios", redirige directamente a crear solicitud
        if ($user->hasRole('usuarios')) {
            return redirect()->route('admin.service.request');
        }

        // Verifica permiso para ver el dashboard
        $this->checkAuthorization($user, ['dashboard.view']);

        // Carga la vista con los datos
        return view('backend.pages.dashboard.index', [
            'admins'       => Admin::all(),
            'informations' => Information::all(),
        ]);
    }
}
