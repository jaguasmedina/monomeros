<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;

class UserController extends Controller
{
    public function index(): Renderable
    {
        /*$this->checkAuthorization(auth()->user(), ['admin.view']);
        return view('backend.pages.dashboard.index', [
            'admins' => Admin::all(),
            'informations' => Information::all(),
        ]);*/
    }
    public function showformuser(): Renderable
    {
        return view('backend.admin.upload_excel');
    }
}
