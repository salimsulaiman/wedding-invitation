<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    private function generateUsername(string $name): string
    {
        $base = Str::slug($name, '');

        if ($base === '') {
            $base = 'user';
        }

        do {
            $username = $base.random_int(100, 999);
        } while (User::where('username', $username)->exists());

        return $username;
    }

    public function search(Request $request)
    {
        $keyword = trim((string) $request->input('q'));

        $customers = User::query()
            ->where('role', 'user')
            ->where('is_active', true)
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query
                        ->where('username', 'like', "%{$keyword}%")
                        ->orWhere('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->latest()
            ->limit(10)
            ->get(['id', 'username', 'name', 'email']);

        return response()->json($customers);
    }

    public function index(Request $request): Response
    {
        $users = User::query()
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query
                        ->where('name', 'like', "%{$request->search}%")
                        ->orWhere('username', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%");
                });
            })
            ->when($request->role, fn ($q) => $q->where('role', $request->role))
            ->withCount('invitations')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only('search', 'role'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ]);

        $plainPassword = Str::password(length: 8, letters: true, numbers: true, symbols: false);

        $validated['username'] = $this->generateUsername($validated['name']);
        $validated['password'] = $plainPassword;
        $validated['must_change_password'] = true;

        $user = User::create($validated);

        return redirect()
            ->route('admin.users.create')
            ->with('account', [
                'name' => $user->name,
                'username' => $user->username,
                'password' => $plainPassword,
                'phone' => $user->phone,
            ]);
    }

    public function edit(User $user): Response
    {
        $user->load(['accessibleThemeCategories' => function ($query) {
            $query->withPivot(['source', 'order_id', 'granted_at']);
        }]);

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user->only(['id', 'name', 'username', 'email', 'phone', 'role', 'is_active']),
            'themeCategories' => \App\Models\ThemeCategory::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'price']),
            'accessList' => $user->accessibleThemeCategories->map(fn ($category) => [
                'id' => $category->id,
                'name' => $category->name,
                'source' => $category->pivot->source,
                'order_id' => $category->pivot->order_id,
                'granted_at' => $category->pivot->granted_at,
            ]),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'max:50', 'alpha_dash',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        if (! $user->isUser()) {
            $user->accessibleThemeCategories()->detach();
        }

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil dihapus.');
    }

    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->is_active ? 'Akun diaktifkan.' : 'Akun dinonaktifkan.');
    }
}