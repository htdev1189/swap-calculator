<?php

use App\Http\Controllers\SwapController;
use App\Http\Controllers\SwapApiController;
use App\Http\Controllers\SwapImportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [SwapController::class, 'index'])->name('home');
    Route::get('/swap', [SwapController::class, 'swap'])->name('swap');
    Route::delete('/swap/delete/{id}', [SwapController::class, 'destroy'])->name('swap.destroy');
    Route::post('/calculate', [SwapController::class, 'calculate'])->name('swap.calculate');

    Route::get('/history', [SwapController::class, 'history'])->name('swap.history.datatable');
    Route::get('/historyData', [SwapController::class, 'getData'])->name('swap.history.data');
    
    // thongke
    Route::get('/statistics', [SwapController::class, 'statistics'])->name('swap.history.statistics');
    
    // swap import
    Route::get('/swap/import', [SwapImportController::class, 'index'])->name('swap.import');
    Route::get('/swap/pairs', [SwapImportController::class, 'getData'])->name('swap.pairs.data');
    Route::post('/swap/import', [SwapImportController::class, 'import'])->name('swap.pairs.import');
    Route::get('/swap-pair/{pair}', [SwapImportController::class, 'getPair'])->name('swap.pairs.get');

});


