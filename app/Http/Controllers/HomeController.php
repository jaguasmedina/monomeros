<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function redirectAdmin()
{
    $user = Auth::guard('admin')->user();
    if ($user->hasRole('usuarios')) {
        return redirect()->route('admin.service.request');
    }
    // Para los demás roles: redirige al dashboard
    return redirect()->route('admin.dashboard');
}


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
}
