<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationGuest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WishController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        /** @var Invitation $invitation */
        $invitation = $request->attributes->get('invitation');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:1000'],
            'attendance' => ['nullable', Rule::in(['hadir', 'tidak_hadir', 'ragu'])],
            'guest_slug' => ['nullable', 'string'],
        ]);

        $guest = null;

        if (! empty($validated['guest_slug'])) {
            $guest = InvitationGuest::where('invitation_id', $invitation->id)
                ->where('slug', $validated['guest_slug'])
                ->first();
        }

        $invitation->wishes()->create([
            'guest_id' => $guest?->id,
            'name' => $validated['name'],
            'message' => $validated['message'],
            'attendance' => $validated['attendance'] ?? null,
        ]);

        return back()->with('success', 'Terima kasih atas ucapan dan doanya.');
    }
}