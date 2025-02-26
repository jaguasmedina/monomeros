<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\LogOptions;

class LogController extends Controller
{
    public function index(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['log.view']);
        $logs = [];

        return view('backend.pages.admins.logs', [
            'logs' => $logs,
        ]);
    }
}
