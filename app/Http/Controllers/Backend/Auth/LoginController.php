<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Si usas $redirectTo como fallback, pero redirectTo() se lleva prioridad.
     */
    protected $redirectTo = RouteServiceProvider::ADMIN_DASHBOARD;

    /**
     * Mostrar el formulario de login para admin.
     */
    public function showLoginForm()
    {
        return view('backend.auth.login');
    }

    /**
     * Decide la ruta tras el login según el rol.
     */
    protected function redirectTo()
    {
        $user = Auth::guard('admin')->user();

        if ($user->hasRole('usuarios')) {
            return route('admin.service.request');
        }

        return route('admin.dashboard');
    }

    /**
     * Procesa el login de admin.
     */
    public function login(Request $request)
    {
        // Validar inputs
        $request->validate([
            'email'    => 'required|string|max:50',
            'password' => 'required|string',
        ]);

        // Intentar con email
        if (Auth::guard('admin')->attempt(
            ['email' => $request->email, 'password' => $request->password],
            $request->filled('remember')
        )) {
            $user = Auth::guard('admin')->user();
            activity('auth')
                ->causedBy($user)
                ->performedOn($user)
                ->withProperties(['email' => $request->email])
                ->log('Administrador se logeo exitosamente.');
            session()->flash('success', 'Has iniciado sesión correctamente.');
            return redirect()->intended($this->redirectPath());
        }

        // Intentar con username
        if (Auth::guard('admin')->attempt(
            ['username' => $request->email, 'password' => $request->password],
            $request->filled('remember')
        )) {
            $user = Auth::guard('admin')->user();
            activity('auth')
                ->causedBy($user)
                ->performedOn($user)
                ->withProperties(['username' => $request->email])
                ->log('El administrador inició sesión usando su usuario.');
            session()->flash('success', 'Has iniciado sesión correctamente.');
            return redirect()->intended($this->redirectPath());
        }

        // Falló login
        activity('auth')
            ->withProperties(['username' => $request->email])
            ->log('Intento de inicio de sesión fallido para el administrador.');
        session()->flash('error', 'Usuario o clave incorrectos.');
        return back()->withInput(['email' => $request->email]);
    }

    /**
     * Logout del guard admin.
     */
    public function logout()
    {
        $user = Auth::guard('admin')->user();
        activity('auth')
            ->causedBy($user)
            ->performedOn($user)
            ->log('Administrador cerró sesión.');
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
