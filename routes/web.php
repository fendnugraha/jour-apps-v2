<?php

use Livewire\Livewire;
use App\Livewire\SearchTable;
use App\Livewire\DisplayCashBank;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PayableController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ReceivableController;
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

// Livewire::routes();
Livewire::component('search-table', SearchTable::class);
Route::get('/testing', fn () => view('home.oldadmin', ['title' => 'Testing']))->middleware('auth');

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
Route::get('/home/administrator', [AccountTraceController::class, 'administrator'])->middleware('auth');
Route::post('/home/generalledger', [AccountTraceController::class, 'generalLedger'])->middleware('auth');
Route::post('/home/reportcabang', [AccountTraceController::class, 'reportcabang'])->middleware('auth');
Route::post('/home/reporttrxcabang', [AccountTraceController::class, 'reporttrxcabang'])->middleware('auth');
Route::get('/home/{id}/transfer', [AccountTraceController::class, 'transfersaldo'])->middleware('auth');
Route::get('/setting', function () {
    return view('home.setting', [
        'title' => 'Setting',
    ]);
})->middleware('auth');
Route::post('/report/customreport', [AccountTraceController::class, 'customReport'])->middleware('auth');


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
Route::post('/transaksi', [AccountTraceController::class, 'transaksi'])->middleware('auth');
Route::post('/mutasi', [AccountTraceController::class, 'mutasi'])->middleware('auth');
Route::post('/pengeluaran', [AccountTraceController::class, 'pengeluaran'])->middleware('auth');
Route::post('/adminbank', [AccountTraceController::class, 'adminbank'])->middleware('auth');
Route::get('/home/{id}/edit', [AccountTraceController::class, 'edit'])->middleware('auth');
Route::put('/home/{id}/edit', [AccountTraceController::class, 'update'])->name('accounttrace.update')->middleware('auth');
Route::delete('/deleteAccountTrace/{id}', [AccountTraceController::class, 'destroy'])->name('accounttrace.delete')->middleware('auth');

// End Journal Area

// Product Area

Route::get('/setting/product', [ProductController::class, 'index'])->middleware('auth');
Route::post('/product/addproduct', [ProductController::class, 'store'])->middleware('auth');
Route::get('/product/{id}/edit', [ProductController::class, 'edit'])->middleware('auth');
Route::put('/product/{id}/edit', [ProductController::class, 'update'])->name('product.update')->middleware('auth');
Route::delete('/product/{id}/delete', [ProductController::class, 'destroy'])->name('product.delete')->middleware('auth');

// End Product Area

// Hutang Area
Route::controller(PayableController::class)->group(function () {
    Route::get('/hutang', 'index')->middleware('auth');
    Route::get('/hutang/{id}/invoice', 'invoice')->middleware('auth');
    Route::get('/hutang/add', 'create')->middleware('auth');
    Route::post('/hutang/add', 'store')->middleware('auth');
    Route::get('/hutang/{id}/detail', 'detail')->middleware('auth');
    Route::get('/hutang/{id}/edit', 'edit')->middleware('auth');
    Route::put('/hutang/{id}/edit', 'update')->name('hutang.update')->middleware('auth');
    Route::delete('/hutang/{id}/delete', 'destroy')->name('hutang.delete')->middleware('auth');

    Route::post('hutang/payment', 'payment')->middleware('auth');
});

// End Hutang Area
// ========================================================================================================

// Piutang Area
Route::controller(ReceivableController::class)->group(function () {
    Route::get('/piutang', 'index')->middleware('auth');
    Route::get('/piutang/{id}/invoice', 'invoice')->middleware('auth');
    Route::get('/piutang/addPiutang', 'addReceivable')->middleware('auth');
    Route::post('/piutang/addPiutang', 'store')->middleware('auth');
    Route::get('/piutang/{id}/edit', 'edit')->middleware('auth');
    Route::put('/piutang/{id}/edit', 'update')->name('piutang.update')->middleware('auth');
    Route::delete('/piutang/{id}/delete', 'destroy')->name('piutang.delete')->middleware('auth');
    Route::get('/piutang/{id}/detail', 'detail')->middleware('auth');

    Route::post('piutang/payment', 'storePayment')->middleware('auth');

    Route::get('/piutang/addReceivableDeposit', 'addReceivableDeposit')->middleware('auth');

    Route::get('/piutang/addReceivableSales', 'addReceivableSales')->middleware('auth');
    Route::post('/piutang/addReceivableSales', 'storeReceivableSales')->middleware('auth');

    Route::get('receivable/export/', 'export')->middleware('auth');
});

// End Piutang Area
// ========================================================================================================
