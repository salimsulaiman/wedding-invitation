<?php

use App\Http\Controllers\User\BankAccountController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\InvitationController;
use App\Http\Controllers\User\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'customer'])
    ->prefix('dashboard')
    ->name('user.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Undangan milik user sendiri
        Route::get('invitations', [InvitationController::class, 'index'])->name('invitations.index');
        Route::post('invitations', [InvitationController::class, 'store'])->name('invitations.store');

        // History pemesanan & masa aktif
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');

        // Rekening tersimpan di dashboard user
        Route::get('bank-accounts', [BankAccountController::class, 'index'])->name('bank-accounts.index');
        Route::post('bank-accounts', [BankAccountController::class, 'store'])->name('bank-accounts.store');
        Route::delete('bank-accounts/{bankAccount}', [BankAccountController::class, 'destroy'])
            ->name('bank-accounts.destroy');
    });