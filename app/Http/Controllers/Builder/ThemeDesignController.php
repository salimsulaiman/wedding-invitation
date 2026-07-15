<?php

namespace App\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Theme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ThemeDesignController extends Controller
{
    public function update(Request $request, Invitation $invitation): RedirectResponse
    {
        $validated = $request->validate([
            'theme_id' => ['required', 'exists:themes,id'],
        ]);

        $theme = Theme::findOrFail($validated['theme_id']);

        $hasAccess = $invitation->user->accessibleThemeCategories()
            ->where('theme_categories.id', $theme->theme_category_id)
            ->exists();

        if (! $hasAccess) {
            return back()->with('error', 'Customer belum memiliki akses ke paket tema tersebut.');
        }

        $invitation->update(['theme_id' => $theme->id]);

        return back()->with('success', 'Tema berhasil diperbarui.');
    }

    public function storeMusic(Request $request, Invitation $invitation): RedirectResponse
    {

       $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'file' => [
                'required',
                'file',
                'mimes:mp3,wav,ogg,m4a,mp4',
                'max:10240',
            ],
        ]);

        if ($invitation->music) {
            Storage::disk('public')->delete($invitation->music->file_path);
        }

        $invitation->music()->updateOrCreate([], [
            'title' => $request->title,
            'file_path' => $request->file('file')->store('music', 'public'),
        ]);

        return back()->with('success', 'Backsound berhasil diunggah.');
    }

    public function destroyMusic(Invitation $invitation): RedirectResponse
    {
        if ($invitation->music) {
            Storage::disk('public')->delete($invitation->music->file_path);
            $invitation->music->delete();
        }

        return back()->with('success', 'Backsound berhasil dihapus.');
    }
}