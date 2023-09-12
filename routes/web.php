<?php

use App\Models\Appointment;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\AppointmentController;

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

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/admin/dashboard', function () {
//     return view('dashboard');
// });
Route::middleware('auth')->group(function() {
    Route::get('/api/users', [UserController::class, 'index']);
    Route::post('/api/users', [UserController::class, 'store']);
    Route::put('/api/users/{user}', [UserController::class, 'update']);
    Route::patch('/api/users/{user}/change_role', [UserController::class, 'changeRole']);
    Route::delete('/api/users/{user}', [UserController::class, 'destory']);
    Route::delete('/api/users', [UserController::class, 'bulkDelete']);

    Route::resource('/api/clients', ClientController::class)->only(['index']);;
    Route::resource('/api/appointments', AppointmentController::class);
    Route::get('/api/appointment_status', [AppointmentController::class, 'getStatusWithCount']);
});


Route::get('{view}', ApplicationController::class)->where('view','(.*)')->middleware('auth');