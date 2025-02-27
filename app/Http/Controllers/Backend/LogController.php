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
use App\Models\Admin;
class LogController extends Controller
{
    public function index(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['log.view']);
        $startDate =now()->subDays(7)->toDateString(); //$request->input('start_date', now()->subDays(7)->toDateString());
        $endDate = now()->endOfDay(); //$request->input('end_date', now()->toDateString());
        $logs = Activity::whereBetween('created_at', [$startDate, $endDate])
        ->latest()
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($log) {
            if ($log->subject_id) {
                $log->subject = Admin::find($log->subject_id);
            }
            if ($log->causer_id) {
                $log->causer = Admin::find($log->causer_id);
            }

            return $log;
        });

        return view('backend.pages.admins.logs', [
            'logs' => $logs,
        ]);
    }
}
