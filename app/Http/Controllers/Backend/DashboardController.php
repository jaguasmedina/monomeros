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

        // Redirigir a crear solicitud si rol = usuarios
        if ($user->hasRole('usuarios')) {
            return redirect()->route('admin.service.request');
        }

        // Autorización normal
        $this->checkAuthorization($user, ['dashboard.view']);

        return view(
            'backend.pages.dashboard.index',
            [
                'admins'       => Admin::all(),
                'informations' => Information::all(),
            ]
        );
    }
}
