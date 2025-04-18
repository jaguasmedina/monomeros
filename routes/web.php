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

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Administración básica
    Route::resource('roles', RolesController::class);
    Route::resource('admins', AdminsController::class);
    Route::resource('informations', InformationController::class);
    Route::post('informations/import', [InformationController::class, 'import'])->name('informations.import');
    
    // Logs
    Route::get('logs', [LogController::class, 'index'])->name('logs.index');

    // Rutas para Visualizador (accesible solo para usuarios con rol "visualizador")
    Route::middleware(['role:visualizador,admin'])->group(function () {
        Route::get('visualizador', [\App\Http\Controllers\Backend\VisualizadorController::class, 'report'])
             ->name('visualizador.report');
    });

    // Gestión de solicitudes (accesible para usuarios con permisos generales)
    Route::prefix('service')->name('service.')->group(function () {
        Route::get('request', [UserServiceController::class, 'request'])->name('request');
        Route::post('store', [UserServiceController::class, 'store'])->name('store');
        Route::match(['get', 'post'], 'query', [UserServiceController::class, 'handleQuery'])->name('query');
        Route::get('edit/{id}', [UserServiceController::class, 'edit'])->name('edit')->middleware('can:admin.edit');
        Route::post('update', [UserServiceController::class, 'update'])->name('update');
        Route::get('documento-final/{id}', [UserServiceController::class, 'generarDocumentoFinal'])
            ->name('documento.final')
            ->middleware('can:admin.view');
    });

    // Rutas para usuarios con rol "usuario"
    Route::middleware(['role:usuarios,admin'])->group(function () {
        // "Mis Solicitudes": muestra las solicitudes creadas por el usuario autenticado
        Route::get('mis-solicitudes', [UserServiceController::class, 'misSolicitudes'])->name('mis_solicitudes');
    });

    // Rutas para Analistas
    Route::prefix('analists')->group(function () {
        Route::get('index', [AnalistsController::class, 'index'])->name('analists.index');
        Route::get('show/{id}', [AnalistsController::class, 'show'])->name('analists.show');
        Route::post('{id}/save', [AnalistsController::class, 'save'])->name('analists.save');
        Route::post('{id}/savenf', [AnalistsController::class, 'savenf'])->name('analists.savenf');
        Route::delete('deletemember/{id}', [AnalistsController::class, 'eliminarMiembro'])->name('analists.deletemember');
    });

    // Rutas para Aprobadores
    Route::prefix('approver')->group(function () {
        Route::get('index', [ApproverController::class, 'index'])->name('approver.index');
        Route::get('show/{id}', [ApproverController::class, 'show'])->name('approver.show');
        Route::post('save/{id}', [ApproverController::class, 'save'])->name('approver.save');
        Route::get('2/index', [ApproverController::class, 'index2'])->name('approver2.index');
        Route::get('2/show/{id}', [ApproverController::class, 'show2'])->name('approver2.show');
        Route::post('2/save/{id}', [ApproverController::class, 'save2'])->name('approver2.save');
    });

    // Autenticación (fuera del grupo para evitar conflictos)
    Route::get('login', [LoginController::class, 'showLoginForm'])
         ->name('login')
         ->withoutMiddleware('auth:admin');
    // Ruta GET para login/submit que redirige a la página de login (para evitar error 405)
    Route::get('login/submit', function () {
         return redirect()->route('admin.login');
    })->name('login.submit')->withoutMiddleware('auth:admin');
    Route::post('login/submit', [LoginController::class, 'login'])
         ->name('login.submit.post')
         ->withoutMiddleware('auth:admin');
    Route::post('logout/submit', [LoginController::class, 'logout'])
         ->name('logout.submit');

    // Recuperación de contraseña
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/reset/submit', [ForgotPasswordController::class, 'reset'])->name('password.update');

    // Rutas solo para usuarios con rol superadmin: Carga de Excel y Reportes
    Route::middleware(['role:superadmin,admin'])->group(function () {
        // Carga de Excel
        Route::get('upload-excel', function () {
            return view('backend.pages.dashboard.upload_excel');
        })->name('informations.upload_excel');

        Route::middleware(['role:superadmin'])->group(function () {
            Route::get('movimientos', [HistorialMovimientosController::class, 'index'])->name('movimientos.index');
        });
        

        // Rutas de Reportes y Exportación
        Route::prefix('reports')->name('reports.')->group(function () {
            // Reporte de Información
            Route::get('informacion', [InformationController::class, 'report'])->name('informacion');
            Route::get('informacion/export', [InformationController::class, 'exportReport'])->name('informacion.export');
            // Reporte de Solicitudes
            Route::get('solicitudes', [UserServiceController::class, 'report'])->name('solicitudes');
            Route::get('solicitudes/export', [UserServiceController::class, 'exportReport'])->name('solicitudes.export');
            // Reporte de Miembros
            Route::get('miembros', [AnalistsController::class, 'report'])->name('miembros');
            Route::get('miembros/export', [AnalistsController::class, 'exportReport'])->name('miembros.export');
        });
    });
});
