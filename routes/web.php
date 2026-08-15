<?php

use App\Http\Controllers\StockDonationPublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->to('admin');
});

Route::get('doacoes', [StockDonationPublicController::class, 'create'])->name('stock-donations.create');
Route::post('doacoes', [StockDonationPublicController::class, 'store'])->name('stock-donations.store');
