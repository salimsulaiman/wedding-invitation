<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvitationController extends Controller
{
    public function index(Request $request): Response
    {
        $invitations = Invitation::query()
            ->with(['user:id,name', 'theme:id,name,theme_category_id', 'theme.category:id,name'])
            ->withCount(['guests', 'wishes'])
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$request->search}%")))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Invitations/Index', [
            'invitations' => $invitations,
            'filters' => $request->only('search', 'status'),
        ]);
    }

    public function create(): Response
    {
        $customers = User::where('role', 'user')
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(function (User $customer) {
                $customer->accessible_theme_category_ids = $customer->accessibleThemeCategories()->pluck('theme_categories.id');
                return $customer;
            });

        $themes = Theme::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'theme_category_id', 'thumbnail']);

        return Inertia::render('Admin/Invitations/Create', [
            'customers' => $customers,
            'themes' => $themes,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'theme_id' => ['nullable', 'exists:themes,id'],
            'name' => ['required', 'string', 'max:255'],
            'max_guest' => ['nullable', 'integer', 'min:1'],
            'expired_at' => ['nullable', 'date'],
        ]);

        if (! empty($validated['theme_id'])) {
            $theme = Theme::findOrFail($validated['theme_id']);
            $customer = User::findOrFail($validated['user_id']);

            $hasAccess = $customer->accessibleThemeCategories()
                ->where('theme_categories.id', $theme->theme_category_id)
                ->exists();

            if (! $hasAccess) {
                return back()
                    ->withErrors(['theme_id' => 'Customer ini belum memiliki akses ke paket tema tersebut.'])
                    ->withInput();
            }
        }

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        $invitation = Invitation::create($validated);

        return redirect()->route('builder.index', $invitation->id)
            ->with('success', 'Undangan berhasil dibuat, lanjutkan pengisian di builder.');
    }

    public function show(Invitation $invitation): Response
    {
        $invitation->load([
            'user:id,name,email,phone',
            'theme:id,name,theme_category_id',
            'theme.category:id,name',
            'domain',
            'couples',
            'events',
            'accounts',
            'giftAddress',
            'music',
            'orders',
        ])->loadCount(['guests', 'wishes']);

        return Inertia::render('Admin/Invitations/Show', [
            'invitation' => $invitation,
        ]);
    }

    public function destroy(Invitation $invitation): RedirectResponse
    {
        $invitation->delete();

        return redirect()->route('admin.invitations.index')->with('success', 'Undangan berhasil dihapus.');
    }

    public function toggleActive(Invitation $invitation): RedirectResponse
    {
        $invitation->update(['is_active' => ! $invitation->is_active]);

        return back()->with('success', $invitation->is_active ? 'Undangan diaktifkan.' : 'Undangan dinonaktifkan.');
    }
}