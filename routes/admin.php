<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DomainController;
use App\Http\Controllers\Admin\GuestController;
use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ThemeCategoryAccessController;
use App\Http\Controllers\Admin\ThemeCategoryController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Kelola akun user/customer
        Route::resource('users', UserController::class)->except('show');
        Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])
            ->name('users.toggle-active');

        // Jenis tema & tema
        Route::resource('theme-categories', ThemeCategoryController::class)->except('show');
        Route::resource('themes', ThemeController::class);

        // Buka/tutup akses PAKET (kategori tema) untuk customer tertentu
        Route::post('theme-categories/{themeCategory}/access', [ThemeCategoryAccessController::class, 'store'])
            ->name('theme-categories.access.store');
        Route::patch('theme-categories/{themeCategory}/access/{user}', [ThemeCategoryAccessController::class, 'update'])
            ->name('theme-categories.access.update');
        Route::delete('theme-categories/{themeCategory}/access/{user}', [ThemeCategoryAccessController::class, 'destroy'])
            ->name('theme-categories.access.destroy');

        // History pemesanan
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}', [OrderController::class, 'update'])->name('orders.update');

        // Undangan (list semua + admin bisa buatkan untuk user)
        Route::get('invitations', [InvitationController::class, 'index'])->name('invitations.index');
        Route::get('invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
        Route::post('invitations', [InvitationController::class, 'store'])->name('invitations.store');
        Route::get('invitations/{invitation}', [InvitationController::class, 'show'])->name('invitations.show');
        Route::delete('invitations/{invitation}', [InvitationController::class, 'destroy'])->name('invitations.destroy');
        Route::patch('invitations/{invitation}/toggle-active', [InvitationController::class, 'toggleActive'])
            ->name('invitations.toggle-active');

        // Domain: admin bisa nonaktifkan kapan saja
        Route::get('domains', [DomainController::class, 'index'])->name('domains.index');
        Route::patch('domains/{domain}/toggle', [DomainController::class, 'toggle'])->name('domains.toggle');

        // Rekap tamu & pesan lintas semua undangan
        Route::get('guests', [GuestController::class, 'index'])->name('guests.index');
    });