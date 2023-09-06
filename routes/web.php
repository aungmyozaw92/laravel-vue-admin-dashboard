<?php

use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

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

Route::get('/api/users', [UserController::class, 'index']);
Route::post('/api/users', [UserController::class, 'store']);
Route::put('/api/users/{user}', [UserController::class, 'update']);
Route::patch('/api/users/{user}/change_role', [UserController::class, 'changeRole']);
Route::delete('/api/users/{user}', [UserController::class, 'destory']);
Route::delete('/api/users', [UserController::class, 'bulkDelete']);

Route::get('{view}', ApplicationController::class)->where('view','(.*)');