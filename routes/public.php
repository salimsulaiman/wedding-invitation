<?php

use App\Http\Controllers\Public\InvitationPageController;
use App\Http\Controllers\Public\WishController;
use Illuminate\Support\Facades\Route;

Route::middleware('invitation.active')
    ->where(['domain', '[a-z0-9\-]+'])
    ->group(function () {
        Route::get('/{domain}', [InvitationPageController::class, 'show'])
            ->name('public.invitation.show');

        Route::post('/{domain}/wishes', [WishController::class, 'store'])
            ->name('public.invitation.wishes.store');
    });