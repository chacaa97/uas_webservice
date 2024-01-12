<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth', 'isAdmin'])->group(function() {
    
    Route::get('/admin', [App\Http\Controllers\HomeController::class, 'admin'])->name('admin');
    Route::get('/pesanan', [App\Http\Controllers\HomeController::class, 'pesanan'])->name('pesanan');
    Route::post('/tambah-barang', [App\Http\Controllers\HomeController::class, 'tambah_barang'])->name('tambah-barang');
    Route::get('/hapus-barang/{id}', [App\Http\Controllers\HomeController::class, 'hapus_barang'])->name('hapus-barang');
});

Route::get('/barang', [App\Http\Controllers\HomeController::class, 'barang'])->name('barang');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/cart', [App\Http\Controllers\HomeController::class, 'cart'])->name('cart');
Route::post('/cart', [App\Http\Controllers\HomeController::class, 'cart_ongkir'])->name('cek-ongkir');
Route::post('/checkout', [App\Http\Controllers\HomeController::class, 'checkout'])->name('checkout');

Route::get('/auth/redirect', [App\Http\Controllers\Auth\LoginController::class, 'redirectToProvider']);
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\LoginController::class, 'handleProviderCallback']);