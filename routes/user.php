<?php

use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\InvitationController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'customer'])
    ->prefix('dashboard')
    ->name('user.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('invitations', [InvitationController::class, 'index'])->name('invitations.index');
        Route::get('invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
        Route::post('invitations', [InvitationController::class, 'store'])->name('invitations.store');
        Route::get('invitations/{invitation}', [InvitationController::class, 'show'])->name('invitations.show');

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    });