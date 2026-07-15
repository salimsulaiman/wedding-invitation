<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationGuest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvitationPageController extends Controller
{
    public function show(Request $request): Response
    {
        /** @var Invitation $invitation */
        $invitation = $request->attributes->get('invitation');

        $invitation->load([
            'theme:id,name,component_key',
            'domain',
            'couples',
            'events',
            'galleries',
            'coverPhotos',
            'accounts',
            'giftAddress',
            'music',
            'wishes' => fn ($query) => $query->latest(),
            'loveStories',
        ]);

        $guest = null;
        

        if ($guestSlug = $request->query('to')) {
            $guest = InvitationGuest::where('invitation_id', $invitation->id)
                ->where('slug', $guestSlug)
                ->first();

            if ($guest && ! $guest->opened_at) {
                $guest->update(['opened_at' => now()]);
            }
        }

        $componentKey = $invitation->theme?->component_key ?? 'default';

        return Inertia::render("Themes/{$componentKey}/Show", [
            'invitation' => $invitation,
            'guest' => $guest,
        ]);
    }
}