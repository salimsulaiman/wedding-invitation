<?php

namespace App\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\DomainHistory;
use App\Models\Invitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function check(Request $request, Invitation $invitation): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/'],
        ]);

        $taken = Domain::where('name', $validated['name'])
            ->where('invitation_id', '!=', $invitation->id)
            ->exists();

        return response()->json(['available' => ! $taken]);
    }

    public function update(Request $request, Invitation $invitation): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/'],
        ]);

        $taken = Domain::where('name', $validated['name'])
            ->where('invitation_id', '!=', $invitation->id)
            ->exists();

        if ($taken) {
            return back()->withErrors(['name' => 'Domain ini sudah digunakan undangan lain.']);
        }

        $oldName = $invitation->domain?->name;

        $domain = $invitation->domain()->updateOrCreate([], [
            'name' => $validated['name'],
            'is_active' => true,
            'activated_at' => now(),
            'deactivated_at' => null,
        ]);

        if ($oldName !== $validated['name']) {
            DomainHistory::create([
                'invitation_id' => $invitation->id,
                'domain_id' => $domain->id,
                'old_name' => $oldName,
                'new_name' => $validated['name'],
                'changed_by' => auth()->id(),
                'note' => $oldName ? 'Domain diubah oleh pengguna' : 'Domain pertama kali diset',
            ]);
        }

        return back()->with('success', 'Domain berhasil disimpan.');
    }
}