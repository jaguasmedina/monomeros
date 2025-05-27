<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\HistorialMovimientosController;


use App\Http\Controllers\Backend\{
    AdminsController,
    LogController,
    DashboardController,
    RolesController,
    InformationController,
    UserServiceController,
    AnalistsController,
    ApproverController
};
use App\Http\Controllers\Backend\Auth\{
    ForgotPasswordController,
    LoginController
};

Auth::routes();

Route::get('/', 'HomeController@redirectAdmin')->name('index');
Route::get('/home', 'HomeController@index')->name('home');

Route::group([
    'prefix'     => 'admin',
    'as'         => 'admin.',
    'middleware' => 'auth:admin',
], function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Administración básica
    Route::resource('roles', RolesController::class);
    Route::resource('admins', AdminsController::class);
    Route::resource('informations', InformationController::class);
    Route::post('informations/import', [InformationController::class, 'import'])
         ->name('informations.import');

    // Logs
    Route::get('logs', [LogController::class, 'index'])->name('logs.index');

    // Visualizador (rol visualizador o admin)
    Route::middleware(['role:visualizador,admin'])->group(function () {
        Route::get('visualizador', [\App\Http\Controllers\Backend\VisualizadorController::class, 'report'])
             ->name('visualizador.report');
    });

    // Gestión de solicitudes
    Route::prefix('service')->name('service.')->group(function () {
        Route::get('request', [UserServiceController::class, 'request'])->name('request');
        Route::post('store', [UserServiceController::class, 'store'])->name('store');
        Route::match(['get','post'], 'query', [UserServiceController::class, 'handleQuery'])
             ->name('query');
        Route::get('edit/{id}', [UserServiceController::class, 'edit'])
             ->middleware('can:admin.edit')
             ->name('edit');
        Route::post('update', [UserServiceController::class, 'update'])->name('update');
        Route::get('documento-final/{id}', [UserServiceController::class, 'generarDocumentoFinal'])
             ->middleware('can:admin.view')
             ->name('documento.final');
    });

    // "Mis Solicitudes" (rol usuarios)
    Route::middleware(['role:usuarios,admin'])->group(function () {
        Route::get('mis-solicitudes', [UserServiceController::class, 'misSolicitudes'])
             ->name('mis_solicitudes');
    });

    // Analistas
    Route::prefix('analists')->group(function () {
        Route::get('index', [AnalistsController::class, 'index'])->name('analists.index');
        Route::get('show/{id}', [AnalistsController::class, 'show'])->name('analists.show');
        Route::post('{id}/save', [AnalistsController::class, 'save'])->name('analists.save');
        Route::post('{id}/savenf', [AnalistsController::class, 'savenf'])->name('analists.savenf');
        Route::delete('deletemember/{id}', [AnalistsController::class, 'eliminarMiembro'])
             ->name('analists.deletemember');
    });

              // Aprobadores
    Route::prefix('approver')->group(function () {
     // Bandeja SAGRILAFT
     Route::get('index',       [ApproverController::class, 'index' ])->name('approver.index');
     Route::get('show/{id}',   [ApproverController::class, 'show'  ])->name('approver.show');
     Route::post('save/{id}',  [ApproverController::class, 'save'  ])->name('approver.save');

     // Bandeja PTEE
     Route::get('2/index',     [ApproverController::class, 'index2'])->name('approver2.index');
     Route::get('2/show/{id}', [ApproverController::class, 'show'  ])->name('approver2.show');
     Route::post('2/save/{id}',[ApproverController::class, 'save'  ])->name('approver2.save');
 });      
 

        // Historial Movimientos...
    Route::middleware(['role:superadmin|admin|analista'])
    ->get('movimientos', [HistorialMovimientosController::class, 'index'])
    ->name('movimientos.index');


    // Autenticación (login / logout)
    Route::get('login', [LoginController::class, 'showLoginForm'])
         ->name('login')
         ->withoutMiddleware('auth:admin');
    Route::get('login/submit', fn() => redirect()->route('admin.login'))
         ->name('login.submit')
         ->withoutMiddleware('auth:admin');
    Route::post('login/submit', [LoginController::class, 'login'])
         ->name('login.submit.post')
         ->withoutMiddleware('auth:admin');
    Route::post('logout/submit', [LoginController::class, 'logout'])
         ->name('logout.submit');

    // Recuperación de contraseña
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
         ->name('password.request');
    Route::post('password/reset/submit', [ForgotPasswordController::class, 'reset'])
         ->name('password.update');

    // Carga de Excel y reportes (superadmin y admin)
    Route::middleware(['role:superadmin,admin'])->group(function () {
        Route::get('upload-excel', fn() => view('backend.pages.dashboard.upload_excel'))
             ->name('informations.upload_excel');

        // Reportes y exportaciones
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('informacion', [InformationController::class, 'report'])
                 ->name('informacion');
            Route::get('informacion/export', [InformationController::class, 'exportReport'])
                 ->name('informacion.export');

            Route::get('solicitudes', [UserServiceController::class, 'report'])
                 ->name('solicitudes');
            Route::get('solicitudes/export', [UserServiceController::class, 'exportReport'])
                 ->name('solicitudes.export');

            Route::get('miembros', [AnalistsController::class, 'report'])
                 ->name('miembros');
            Route::get('miembros/export', [AnalistsController::class, 'exportReport'])
                 ->name('miembros.export');
        });
    });
});
