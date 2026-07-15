<?php

namespace App\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use App\Http\Requests\Builder\CoupleEventRequest;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CoupleEventController extends Controller
{
    public function update(CoupleEventRequest $request, Invitation $invitation): RedirectResponse
    {
        $validated = $request->validated();

        $invitation->loadMissing('theme.category');
        $allowPhotos = $invitation->theme?->category?->allow_custom_photos ?? true;

        $invitation->update([
            'name' => $validated['name'],
            'quote_text' => $validated['quote_text'] ?? null,
            'quote_source' => $validated['quote_source'] ?? null,
            'maps_link' => $validated['maps_link'] ?? null,
            'streaming_link' => $validated['streaming_link'] ?? null,
        ]);

        foreach (['akad', 'resepsi'] as $type) {
            if (! empty(array_filter($validated[$type] ?? []))) {
                $invitation->events()->updateOrCreate(['type' => $type], $validated[$type]);
            }
        }

        foreach (['groom', 'bride'] as $type) {
            if (! empty($validated[$type]['full_name'])) {
                $data = $validated[$type];

                if ($request->hasFile("{$type}.photo")) {
                    if (! $allowPhotos) {
                        abort(403, 'Paket tema ini tidak mengizinkan upload foto custom.');
                    }
                    $data['photo'] = $request->file("{$type}.photo")->store('couples', 'public');
                } else {
                    unset($data['photo']);
                }

                $invitation->couples()->updateOrCreate(['type' => $type], $data);
            }
        }

        return back()->with('success', 'Data Mempelai & Acara berhasil disimpan.');
    }

    public function storeLoveStory(Request $request, Invitation $invitation): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'story_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('love-stories', 'public');
        }

        $validated['order'] = $invitation->loveStories()->max('order') + 1;

        $invitation->loveStories()->create($validated);

        return back()->with('success', 'Love story berhasil ditambahkan.');
    }

    public function updateLoveStory(Request $request, Invitation $invitation, InvitationLoveStory $loveStory): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'story_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('photo')) {
            if ($loveStory->photo) {
                Storage::disk('public')->delete($loveStory->photo);
            }
            $validated['photo'] = $request->file('photo')->store('love-stories', 'public');
        }

        $loveStory->update($validated);

        return back()->with('success', 'Love story berhasil diperbarui.');
    }

    public function reorderLoveStory(Request $request, Invitation $invitation): RedirectResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:invitation_love_stories,id'],
        ]);

        foreach ($validated['order'] as $index => $id) {
            InvitationLoveStory::where('id', $id)
                ->where('invitation_id', $invitation->id)
                ->update(['order' => $index]);
        }

        return back()->with('success', 'Urutan love story diperbarui.');
    }
}