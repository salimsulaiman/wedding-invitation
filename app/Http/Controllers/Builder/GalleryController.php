<?php

namespace App\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationGallery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class GalleryController extends Controller
{

    private const MAX_COVER_PHOTOS = 3;

    public function store(Request $request, Invitation $invitation): RedirectResponse
    {

        $invitation->loadMissing('theme.category');

        if (! $invitation->theme?->category?->allow_custom_photos) {
            abort(403, 'Paket tema ini tidak mengizinkan upload foto custom.');
        }
        
        $validated = $request->validate([
            'type' => ['required', Rule::in(['cover', 'gallery'])],
            'photos' => ['required', 'array'],
            'photos.*' => ['image', 'max:4096'],
        ]);

        if ($validated['type'] === 'cover') {
            $existingCoverCount = $invitation->coverPhotos()->count();
            $incomingCount = count($request->file('photos'));

            if ($existingCoverCount + $incomingCount > self::MAX_COVER_PHOTOS) {
                return back()->with(
                    'error',
                    'Foto cover maksimal '.self::MAX_COVER_PHOTOS.' foto. Saat ini sudah ada '.$existingCoverCount.' foto.'
                );
            }
        }

        $order = InvitationGallery::where('invitation_id', $invitation->id)
            ->where('type', $validated['type'])
            ->max('order') ?? 0;

        foreach ($request->file('photos') as $file) {
            $order++;
            $invitation->galleries()->create([
                'type' => $validated['type'],
                'photo' => $file->store('galleries', 'public'),
                'order' => $order,
            ]);
        }

        return back()->with('success', $validated['type'] === 'cover' ? 'Foto cover berhasil ditambahkan.' : 'Foto berhasil ditambahkan.');
    }

    public function destroy(Invitation $invitation, InvitationGallery $gallery): RedirectResponse
    {
        Storage::disk('public')->delete($gallery->photo);
        $gallery->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    public function reorder(Request $request, Invitation $invitation): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['cover', 'gallery'])],
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:invitation_galleries,id'],
        ]);

        foreach ($validated['order'] as $index => $id) {
            InvitationGallery::where('id', $id)
                ->where('invitation_id', $invitation->id)
                ->where('type', $validated['type'])
                ->update(['order' => $index]);
        }

        return back()->with('success', 'Urutan foto diperbarui.');
    }
}