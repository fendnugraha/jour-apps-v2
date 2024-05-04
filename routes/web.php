<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\AccountTraceController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\WarehouseAccountController;

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

// Login Area
Route::get('/', [AuthController::class, 'index'])->middleware('guest');

Route::post('/', [AuthController::class, 'authenticate'])->name('login')->middleware('guest');
Route::post('/auth/logout', [AuthController::class, 'logout']);

Route::get('/auth/register', [AuthController::class, 'register'])->middleware('guest');
Route::post('/auth/register', [AuthController::class, 'store']);

Route::get('/auth/register_success', function () {
    return view('auth/register_success');
})->middleware('guest');

// End Login Area
// ========================================================================================================

// Home Area
Route::get('/home', [AccountTraceController::class, 'index'])->middleware('auth');
Route::get('/home/dailyreport', [AccountTraceController::class, 'dailyreport'])->middleware('auth');
Route::get('/setting', function () {
    return view('home.setting', [
        'title' => 'Setting',
    ]);
})->middleware('auth');


// End Home Area
// ========================================================================================================

// ChartOfAccount Area

Route::get('/setting/accounts', [ChartOfAccountController::class, 'index'])->middleware('auth');
Route::get('/setting/accounts/add', [ChartOfAccountController::class, 'addaccount'])->middleware('auth');
Route::post('/setting/accounts/add', [ChartOfAccountController::class, 'store'])->middleware('auth');
Route::get('/setting/accounts/{id}/edit', [ChartOfAccountController::class, 'edit'])->middleware('auth');
Route::put('/setting/accounts/{id}/edit', [ChartOfAccountController::class, 'update'])->name('coa.update')->middleware('auth');
Route::delete('/setting/accounts/{id}/delete', [ChartOfAccountController::class, 'destroy'])->name('coa.delete')->middleware('auth');

// End ChartOfAccount Area
// ========================================================================================================

// Contact Area

Route::get('/setting/contacts', [ContactController::class, 'index'])->middleware('auth');
Route::post('/setting/contacts/add', [ContactController::class, 'store'])->middleware('auth');
Route::get('/setting/contacts/{id}/edit', [ContactController::class, 'edit'])->middleware('auth');
Route::put('/setting/contacts/{id}/edit', [ContactController::class, 'update'])->name('contact.update')->middleware('auth');
Route::delete('/setting/contacts/{id}/delete', [ContactController::class, 'destroy'])->name('contact.delete')->middleware('auth');

// End Contact Area
// ========================================================================================================

// User Area

Route::get('/setting/users', [AuthController::class, 'users'])->middleware('auth');
Route::post('/setting/user/add', [AuthController::class, 'store'])->middleware('auth');
Route::get('/setting/user/{id}/edit', [AuthController::class, 'edit'])->middleware('auth');
Route::put('/setting/user/{id}/edit', [AuthController::class, 'update'])->name('user.update')->middleware('auth');
Route::delete('/setting/user/{id}/delete', [AuthController::class, 'destroy'])->name('user.delete')->middleware('auth');

// End User Area
// ========================================================================================================

// Warehouse Area

Route::get('/setting/warehouses', [WarehouseController::class, 'index'])->middleware('auth');
Route::get('/setting/warehouse/{id}/details', [WarehouseController::class, 'details'])->middleware('auth');
Route::post('/setting/warehouse/add', [WarehouseController::class, 'store'])->middleware('auth');
Route::get('/setting/warehouse/{id}/edit', [WarehouseController::class, 'edit'])->middleware('auth');
Route::put('/setting/warehouse/{id}/edit', [WarehouseController::class, 'update'])->name('warehouse.update')->middleware('auth');
Route::delete('/setting/warehouse/{id}/delete', [WarehouseController::class, 'destroy'])->name('warehouse.delete')->middleware('auth');

Route::post('/warehouse/addwarehouseaccount', [WarehouseAccountController::class, 'store'])->middleware('auth');
Route::delete('/warehouse/delete/{id}', [WarehouseAccountController::class, 'destroy'])->name('warehouseaccount.delete')->middleware('auth');

// End Warehouse Area
// ========================================================================================================

// Journal Area

Route::post('/addTransfer', [AccountTraceController::class, 'addTransfer'])->middleware('auth');
Route::post('/addTarikTunai', [AccountTraceController::class, 'addTarikTunai'])->middleware('auth');
Route::post('/mutasi', [AccountTraceController::class, 'pengeluaran'])->middleware('auth');
Route::delete('/deleteAccountTrace/{id}', [AccountTraceController::class, 'destroy'])->name('accounttrace.delete')->middleware('auth');

// End Journal Area
