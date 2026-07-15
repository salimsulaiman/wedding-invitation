<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThemeCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ThemeCategoryController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.themes.index');
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('admin.themes.index');
    }

    public function edit(ThemeCategory $themeCategory): RedirectResponse
    {
        return redirect()->route('admin.themes.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'allow_custom_photos' => ['boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']).'-'.Str::random(4);

        ThemeCategory::create($validated);

        return back()->with('success', 'Jenis tema berhasil ditambahkan.');
    }

    public function update(Request $request, ThemeCategory $themeCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'allow_custom_photos' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $themeCategory->update($validated);

        return back()->with('success', 'Jenis tema berhasil diperbarui.');
    }

    public function destroy(ThemeCategory $themeCategory): RedirectResponse
    {
        if ($themeCategory->themes()->exists()) {
            return back()->with('error', 'Tidak bisa hapus, masih ada tema yang menggunakan jenis ini.');
        }

        $themeCategory->delete();

        return back()->with('success', 'Jenis tema berhasil dihapus.');
    }
}