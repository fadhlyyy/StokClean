<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/login/otp', [AuthController::class, 'showOtpForm'])->name('login.otp');
Route::post('/login/otp', [AuthController::class, 'verifyOtp'])->name('login.otp.verify');
Route::post('/login/otp/resend', [AuthController::class, 'resendOtp'])->name('login.otp.resend');
Route::post('/login/otp/cancel', [AuthController::class, 'cancelOtp'])->name('login.otp.cancel');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

if (app()->environment('local')) {
    Route::get('/dev-login', function () {
        $user = \App\Models\User::where('role', 'pemilik')->first();
        \Illuminate\Support\Facades\Auth::login($user);
        return redirect()->route('dashboard');
    });
}

Route::middleware('auth')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('dashboard');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/users', [AuthController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [AuthController::class, 'updateUser'])->name('users.update');

    Route::post('/barang', [InventoryController::class, 'storeBarang'])->name('barang.store');
    Route::put('/barang/{barang}', [InventoryController::class, 'updateBarang'])->name('barang.update');
    Route::delete('/barang/{barang}', [InventoryController::class, 'destroyBarang'])->name('barang.destroy');

    Route::post('/kategori', [InventoryController::class, 'storeKategori'])->name('kategori.store');
    Route::post('/supplier', [InventoryController::class, 'storeSupplier'])->name('supplier.store');
    Route::put('/supplier/{supplier}', [InventoryController::class, 'updateSupplier'])->name('supplier.update');
    Route::delete('/supplier/{supplier}', [InventoryController::class, 'destroySupplier'])->name('supplier.destroy');
    Route::post('/stok-masuk', [InventoryController::class, 'storeStokMasuk'])->name('stok-masuk.store');
    Route::post('/stok-keluar', [InventoryController::class, 'storeStokKeluar'])->name('stok-keluar.store');
    Route::get('/laporan/export', [InventoryController::class, 'exportLaporanCsv'])->name('laporan.export');
    Route::get('/kalender-transaksi', [InventoryController::class, 'getKalenderData'])->name('kalender.data');
});
