<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Theme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvitationController extends Controller
{
    public function index(Request $request): Response
    {
        $invitations = $request->user()->invitations()
            ->with('theme:id,name,thumbnail')
            ->withCount(['guests', 'wishes'])
            ->latest()
            ->get(['id', 'user_id', 'theme_id', 'name', 'status', 'is_active', 'expired_at', 'max_guest', 'created_at']);

        return Inertia::render('User/Invitations/Index', [
            'invitations' => $invitations,
        ]);
    }

    public function create(Request $request): Response
    {
        $user = $request->user();

        $accessibleCategoryIds = $user->accessibleThemeCategories()->pluck('theme_categories.id');

        $themes = Theme::whereIn('theme_category_id', $accessibleCategoryIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'theme_category_id', 'thumbnail']);

        return Inertia::render('User/Invitations/Create', [
            'themes' => $themes,
            'hasAnyAccess' => $accessibleCategoryIds->isNotEmpty(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'theme_id' => ['nullable', 'exists:themes,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        if (! empty($validated['theme_id'])) {
            $theme = Theme::findOrFail($validated['theme_id']);

            $hasAccess = $user->accessibleThemeCategories()
                ->where('theme_categories.id', $theme->theme_category_id)
                ->exists();

            if (! $hasAccess) {
                return back()
                    ->withErrors(['theme_id' => 'Anda belum memiliki akses ke paket tema tersebut.'])
                    ->withInput();
            }
        }

        $invitation = $user->invitations()->create([
            'theme_id' => $validated['theme_id'] ?? null,
            'name' => $validated['name'],
            'status' => 'draft',
        ]);

        return redirect()->route('builder.index', $invitation->id);
    }

    public function show(Invitation $invitation): Response
    {
        abort_unless($invitation->user_id === auth()->id(), 403);

        $invitation->load([
            'theme:id,name,thumbnail',
            'theme.category:id,name',
            'domain',
        ])->loadCount(['guests', 'wishes']);

        return Inertia::render('User/Invitations/Show', [
            'invitation' => $invitation,
        ]);
    }
}