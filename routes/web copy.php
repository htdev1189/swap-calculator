<?php

use App\Http\Controllers\SwapController;
use App\Http\Controllers\SwapImportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RolePermissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    // 🟢 Các route chỉ cho khách (chưa đăng nhập)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
        Route::post('/login', [LoginController::class, 'login'])->name('login');
    });

    // 🟡 Logout route — chỉ cho user đã login
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', [SwapController::class, 'index'])->name('home')->middleware(['auth','prevent-back-history']);
    // 🔒 Các route yêu cầu đăng nhập
    Route::middleware(['auth', 'role:admin|editor' , 'prevent-back-history'])->group(function () {
        Route::middleware('permission:view swap')->get('/swap', [SwapController::class, 'swap'])->name('swap');
        Route::delete('/swap/delete/{id}', [SwapController::class, 'destroy'])->name('swap.destroy');
        Route::middleware('permission:edit swap')->get('/swap/edit/{id}', [SwapController::class, 'edit'])->name('swap.edit');
        Route::post('/swap/update', [SwapController::class, 'update'])->name('swap.update');
        Route::post('/calculate', [SwapController::class, 'calculate'])->name('swap.calculate');

        Route::get('/history', [SwapController::class, 'history'])->name('swap.history.datatable');
        Route::get('/historyData', [SwapController::class, 'getData'])->name('swap.history.data');
        // Route::get('/statistics', [SwapController::class, 'statistics'])->name('swap.history.statistics');
        Route::middleware('permission:view statistic')->get('/statistics', [SwapController::class, 'statistics'])->name('swap.history.statistics');

        // swap import
        Route::get('/swap/import', [SwapImportController::class, 'index'])->name('swap.import');
        Route::get('/swap/pairs', [SwapImportController::class, 'getData'])->name('swap.pairs.data');
        Route::post('/swap/import', [SwapImportController::class, 'import'])->name('swap.pairs.import');
        Route::get('/swap-pair/{pair}', [SwapImportController::class, 'getPair'])->name('swap.pairs.get');

        Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');
        Route::post('/permissions', [RolePermissionController::class, 'storePermission'])->name('permissions.store');
        Route::post('/roles/assign-permissions', [RolePermissionController::class, 'givePermissionToRole'])->name('roles.permissions.assign');
        Route::post('/users/assign-role', [RolePermissionController::class, 'assignRoleToUser'])->name('users.roles.assign');
        Route::delete('/roles/{role}/remove-permission', [RolePermissionController::class, 'removePermissionFromRole'])->name('roles.permissions.remove');
        Route::delete('/roles/{role}/permissions/{permission}', [RolePermissionController::class, 'removePermission'])->name('roles.remove.permission'); // remove per from roles

    });
});
