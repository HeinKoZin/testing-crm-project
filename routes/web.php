<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\MemberController;
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
        Route::get('/show/{id}', [UserController::class, 'show'])->name('users.show');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/update/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/delete/{id}', [UserController::class, 'delete'])->name('users.delete');
        Route::get('/list', [UserController::class, 'getRoleList'])->name('getuserlist');
        Route::get('/export',[UserController::class,  'export'])->name('users.export');
        Route::post('/import', [UserController::class, 'import'])->name('users.import');
        Route::post('/email-integration/{id}', [UserController::class, 'sendEmail'])->name('send.email');
    });
    // User end //

    // Member start //
    Route::group(["prefix" => "members"], function () {
        Route::get('/', [MemberController::class, 'index'])->name('members');
        Route::get('/create', [MemberController::class, 'create'])->name('members.create');
        Route::post('/save', [MemberController::class, 'save'])->name('members.save');
        Route::get('/show/{id}', [MemberController::class, 'show'])->name('members.show');
        Route::get('/edit/{id}', [MemberController::class, 'edit'])->name('members.edit');
        Route::put('/update/{id}', [MemberController::class, 'update'])->name('members.update');
        Route::delete('/delete/{id}', [MemberController::class, 'delete'])->name('members.delete');
        Route::get('/list', [MemberController::class, 'getRoleList'])->name('getmemberlist');
        Route::get('/export',[MemberController::class,  'export'])->name('members.export');
        Route::post('/import', [MemberController::class, 'import'])->name('members.import');
        Route::post('/email-integration/member/{id}', [MemberController::class, 'sendEmail'])->name('members.send.email');
    });
    // Member end //
});

Route::group(["namespace" => "Auth"], function () {
    Route::get('/auth/login', [AuthController::class, 'loginPage'])->name('auth.login.index');
    Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
    Route::get('/member/register', [AuthController::class, 'registerPage'])->name('auth.register.index');
    Route::post('/member/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/member/success', [AuthController::class, 'successMember'])->name('success.member');
});



Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
