<?php

use App\Http\Controllers\Builder\BuilderController;
use App\Http\Controllers\Builder\CoupleEventController;
use App\Http\Controllers\Builder\DigitalEnvelopeController;
use App\Http\Controllers\Builder\DomainController;
use App\Http\Controllers\Builder\GalleryController;
use App\Http\Controllers\Builder\GuestBuilderController;
use App\Http\Controllers\Builder\ThemeDesignController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'invitation.owner'])
    ->prefix('invitations/{invitation}/builder')
    ->name('builder.')
    ->group(function () {
        Route::get('/', [BuilderController::class, 'index'])->name('index');
        Route::get('close', [BuilderController::class, 'close'])->name('close'); // redirect balik ke dashboard sesuai role

        // Mempelai & Acara
        Route::put('couple-event', [CoupleEventController::class, 'update'])->name('couple-event.update');

        // Love Story (nested di tab Mempelai & Acara)
        Route::post('love-stories', [CoupleEventController::class, 'storeLoveStory'])->name('love-stories.store');
        Route::put('love-stories/{loveStory}', [CoupleEventController::class, 'updateLoveStory'])->name('love-stories.update');
        Route::delete('love-stories/{loveStory}', [CoupleEventController::class, 'destroyLoveStory'])->name('love-stories.destroy');
        Route::patch('love-stories/reorder', [CoupleEventController::class, 'reorderLoveStory'])->name('love-stories.reorder');

        // Galeri Foto
        Route::post('gallery', [GalleryController::class, 'store'])->name('gallery.store');
        Route::delete('gallery/{gallery}', [GalleryController::class, 'destroy'])->name('gallery.destroy');
        Route::patch('gallery/reorder', [GalleryController::class, 'reorder'])->name('gallery.reorder');

        // Amplop Digital
        Route::post('accounts', [DigitalEnvelopeController::class, 'storeAccount'])->name('accounts.store');
        Route::delete('accounts/{account}', [DigitalEnvelopeController::class, 'destroyAccount'])->name('accounts.destroy');
        Route::put('gift-address', [DigitalEnvelopeController::class, 'updateGiftAddress'])->name('gift-address.update');

        // Tema Desain + Backsound
        Route::put('theme', [ThemeDesignController::class, 'update'])->name('theme.update');
        Route::post('music', [ThemeDesignController::class, 'storeMusic'])->name('music.store');
        Route::delete('music', [ThemeDesignController::class, 'destroyMusic'])->name('music.destroy');

        // Domain custom
        Route::post('domain/check', [DomainController::class, 'check'])->name('domain.check');
        Route::put('domain', [DomainController::class, 'update'])->name('domain.update');

        // Kelola Tamu
        Route::post('guests', [GuestBuilderController::class, 'store'])->name('guests.store');
        Route::put('guests/{guest}', [GuestBuilderController::class, 'update'])->name('guests.update');
        Route::delete('guests/{guest}', [GuestBuilderController::class, 'destroy'])->name('guests.destroy');

        Route::patch('guests/{guest}/mark-sent', [GuestBuilderController::class, 'markSent'])->name('guests.mark-sent');
    });