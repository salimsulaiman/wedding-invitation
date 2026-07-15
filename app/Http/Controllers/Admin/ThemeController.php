<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use App\Models\ThemeCategory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ThemeController extends Controller
{
    public function index(Request $request): Response
    {
        $themes = Theme::query()
            ->with('category:id,name')
            ->when($request->category_id, fn ($q) => $q->where('theme_category_id', $request->category_id))
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = ThemeCategory::orderBy('name')
            ->withCount('usersWithAccess')
            ->get(['id', 'name', 'price', 'description', 'is_active'])
            ->each(function ($category) {
                $category->access_user_ids = $category->usersWithAccess()->pluck('users.id');
            });

        return Inertia::render('Admin/Themes/Index', [
            'themes' => $themes,
            'categories' => $categories,
            'customers' => User::where('role', 'user')->orderBy('name')->get(['id', 'name', 'email']),
            'filters' => $request->only('search', 'category_id'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Themes/Create', [
            'categories' => ThemeCategory::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $validated['slug'] = Str::slug($validated['name']).'-'.Str::random(4);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('themes', 'public');
        }

        Theme::create($validated);

        return redirect()->route('admin.themes.index')->with('success', 'Tema berhasil ditambahkan.');
    }

    public function edit(Theme $theme): Response
    {
        return Inertia::render('Admin/Themes/Edit', [
            'theme' => $theme,
            'categories' => ThemeCategory::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Theme $theme): RedirectResponse
    {
        $validated = $this->validated($request, $theme->id);

        if ($request->hasFile('thumbnail')) {
            if ($theme->thumbnail) {
                Storage::disk('public')->delete($theme->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('themes', 'public');
        }

        $theme->update($validated);

        return redirect()->route('admin.themes.index')->with('success', 'Tema berhasil diperbarui.');
    }

    public function destroy(Theme $theme): RedirectResponse
    {
        if ($theme->thumbnail) {
            Storage::disk('public')->delete($theme->thumbnail);
        }

        $theme->delete();

        return back()->with('success', 'Tema berhasil dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'theme_category_id' => ['required', 'exists:theme_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'component_key' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
        ]);
    }
}