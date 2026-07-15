<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationGuest;
use App\Models\InvitationWish;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GuestController extends Controller
{
    public function index(Request $request): Response
    {
        $tab = $request->get('tab', 'guests');

        $guests = InvitationGuest::query()
            ->with('invitation:id,name')
            ->when($request->invitation_id, fn ($q) => $q->where('invitation_id', $request->invitation_id))
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(10, ['*'], 'guests_page')
            ->withQueryString();

        $wishes = InvitationWish::query()
            ->with('invitation:id,name')
            ->when($request->invitation_id, fn ($q) => $q->where('invitation_id', $request->invitation_id))
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(10, ['*'], 'wishes_page')
            ->withQueryString();

        return Inertia::render('Admin/Guests/Index', [
            'guests' => $guests,
            'wishes' => $wishes,
            'invitations' => Invitation::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only('search', 'invitation_id', 'tab'),
            'activeTab' => $tab,
        ]);
    }
}