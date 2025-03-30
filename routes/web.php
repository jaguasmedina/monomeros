<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Backend\AdminsController;
use App\Http\Controllers\Backend\LogController;
use App\Http\Controllers\Backend\Auth\ForgotPasswordController;
use App\Http\Controllers\Backend\Auth\LoginController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\RolesController;
use App\Http\Controllers\Backend\InformationController;
use App\Http\Controllers\Backend\UserServiceController;
use App\Http\Controllers\Backend\AnalistsController;
use App\Http\Controllers\Backend\ApproverController;
use App\Http\Controllers\Backend\PdfController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', 'HomeController@redirectAdmin')->name('index');
Route::get('/home', 'HomeController@index')->name('home');

/**
 * Admin routes
 */
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('roles', RolesController::class);
    Route::resource('admins', AdminsController::class);
    Route::get('logs', [LogController::class, 'index'])->name('logs.index');
    Route::resource('informations', InformationController::class);
    Route::post('informations/import', [InformationController::class, 'import'])->name('informations.import');

    Route::post('service/store', [UserServiceController::class, 'store'])->name('service.store');
    Route::get('service/request', [UserServiceController::class, 'request'])->name('service.request');
    Route::get('service/query', [UserServiceController::class, 'query'])->name('service.query');
    Route::post('service/queryreq', [UserServiceController::class, 'queryreq'])->name('service.queryreq');
    Route::get('service/edit/{id}', [UserServiceController::class, 'edit'])->name('service.edit');
    Route::post('service/update/', [UserServiceController::class, 'update'])->name('service.update');
    Route::get('service/descargarpdf/{id}', [PdfController::class, 'generarPDF'])->name('service.descargar.pdf');


    Route::get('analists/index', [AnalistsController::class, 'index'])->name('analists.index');
    Route::get('analists/show/{id}', [AnalistsController::class, 'show'])->name('analists.show');
    Route::post('analists/{id}/save', [AnalistsController::class, 'save'])->name('analists.save');
    Route::post('analists/{id}/savenf', [AnalistsController::class, 'savenf'])->name('analists.savenf');
    Route::delete('analists/deletemember/{id}', [AnalistsController::class, 'eliminarMiembro'])->name('admin.analists.deletemember');

    Route::get('approver/index', [ApproverController::class, 'index'])->name('approver.index');
    Route::get('approver/show/{id}', [ApproverController::class, 'show'])->name('approver.show');
    Route::post('approver/save/{id}', [ApproverController::class, 'save'])->name('approver.save');
    Route::get('approver2/index', [ApproverController::class, 'index2'])->name('approver2.index');
    Route::get('approver2/show/{id}', [ApproverController::class, 'show2'])->name('approver2.show');
    Route::post('approver2/save/{id}', [ApproverController::class, 'save2'])->name('approver2.save');

    // Nueva vista de carga de Excel (solo para superadmin)
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/upload-excel', function () {
            return view('backend.pages.dashboard.upload_excel');
        })->name('informations.upload_excel');
    });

    // Login Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login/submit', [LoginController::class, 'login'])->name('login.submit');

    // Logout Routes
    Route::post('/logout/submit', [LoginController::class, 'logout'])->name('logout.submit');

    // Forget Password Routes
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/reset/submit', [ForgotPasswordController::class, 'reset'])->name('password.update');
})->middleware('auth:admin');
