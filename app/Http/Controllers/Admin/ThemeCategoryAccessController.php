<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThemeCategory;
use App\Models\ThemeCategoryUser;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ThemeCategoryAccessController extends Controller
{
    public function store(Request $request, ThemeCategory $themeCategory): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $user = User::findOrFail($validated['user_id']);

        if (! $user->isUser()) {
            return back()->with('error', 'Akses paket hanya bisa dibuka untuk akun customer.');
        }

        ThemeCategoryUser::firstOrCreate(
            ['theme_category_id' => $themeCategory->id, 'user_id' => $user->id],
            ['granted_by' => auth()->id(), 'granted_at' => now()]
        );

        return back()->with('success', "Paket \"{$themeCategory->name}\" dibuka untuk {$user->name}.");
    }

    public function destroy(ThemeCategory $themeCategory, User $user): RedirectResponse
    {
        ThemeCategoryUser::where('theme_category_id', $themeCategory->id)->where('user_id', $user->id)->delete();

        return back()->with('success', "Akses paket untuk {$user->name} dicabut.");
    }
}