<?php

namespace App\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationGuest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestBuilderController extends Controller
{
    public function store(Request $request, Invitation $invitation): RedirectResponse
    {
        if ($invitation->isGuestLimitReached()) {
            return back()->with('error', 'Jumlah tamu sudah mencapai batas maksimal.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'group' => ['nullable', 'string', 'max:255'],
            'quota' => ['nullable', 'integer', 'min:1'],
        ]);

        $validated['quota'] = $validated['quota'] ?? 1;
        $validated['slug'] = Str::slug($validated['name']).'-'.Str::random(6);

        $invitation->guests()->create($validated);

        return back()->with('success', 'Tamu berhasil ditambahkan.');
    }

    public function update(Request $request, Invitation $invitation, InvitationGuest $guest): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'group' => ['nullable', 'string', 'max:255'],
            'quota' => ['nullable', 'integer', 'min:1'],
        ]);

        $guest->update($validated);

        return back()->with('success', 'Data tamu berhasil diperbarui.');
    }

    public function destroy(Invitation $invitation, InvitationGuest $guest): RedirectResponse
    {
        $guest->delete();

        return back()->with('success', 'Tamu berhasil dihapus.');
    }

    public function markSent(Invitation $invitation, InvitationGuest $guest): RedirectResponse
    {
        $guest->update(['is_sent' => true]);

        return back();
    }
}