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
    /**
     * Daftar undangan (card), diurutkan dari yang terbaru.
     */
    public function index(Request $request): Response
    {
        $invitations = Invitation::query()
            ->with(['user:id,name,username'])
            ->withCount(['guests', 'wishes'])
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;

                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('username', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Admin/Guests/Index', [
            'invitations' => $invitations,
            'filters' => $request->only('search'),
        ]);
    }

    /**
     * Detail tamu & ucapan untuk satu undangan.
     */
    public function show(Request $request, Invitation $invitation): Response
    {
        $tab = $request->get('tab', 'guests');
 
        $guests = InvitationGuest::query()
            ->where('invitation_id', $invitation->id)
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->filled('is_sent'), fn ($q) => $q->where('is_sent', (bool) $request->is_sent))
            ->latest()
            ->paginate(10, ['*'], 'guests_page')
            ->withQueryString();
 
        $wishes = InvitationWish::query()
            ->where('invitation_id', $invitation->id)
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->filled('attendance'), fn ($q) => $q->where('attendance', $request->attendance))
            ->latest()
            ->paginate(10, ['*'], 'wishes_page')
            ->withQueryString();
 
        return Inertia::render('Admin/Guests/Show', [
            'invitation' => $invitation->only('id', 'name'),
            'guests' => $guests,
            'wishes' => $wishes,
            'filters' => $request->only('search', 'is_sent', 'attendance', 'tab'),
            'activeTab' => $tab,
        ]);
    }
}