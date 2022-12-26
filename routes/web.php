<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\UserController;

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


Route::group(["namespace" => "Dashboard", 'middleware' => ['auth']], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Role start //
    Route::group(["prefix" => "roles"], function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles');
        Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/save', [RoleController::class, 'save'])->name('roles.save');
        Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/update/{id}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/delete/{id}', [RoleController::class, 'delete'])->name('roles.delete');
        Route::get('/list', [RoleController::class, 'getRoleList'])->name('getrolelist');
    });
    // Role end //

    // User start //
    Route::group(["prefix" => "users"], function () {
        Route::get('/', [UserController::class, 'index'])->name('users');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/save', [UserController::class, 'save'])->name('users.save');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/update/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/delete/{id}', [UserController::class, 'delete'])->name('users.delete');
        Route::get('/list', [UserController::class, 'getRoleList'])->name('getuserlist');
    });
    // User end //
});

Route::group(["namespace" => "Auth", "prefix" => "auth"], function () {
    Route::get('/login', [AuthController::class, 'loginPage'])->name('auth.login.index');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::get('/register', [AuthController::class, 'registerPage'])->name('auth.register.index');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
