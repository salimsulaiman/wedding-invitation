<?php

namespace App\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DigitalEnvelopeController extends Controller
{
    public function storeAccount(Request $request, Invitation $invitation): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['bank', 'ewallet'])],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:100'],
            'account_holder' => ['required', 'string', 'max:255'],
        ]);

        $invitation->accounts()->create($validated);

        return back()->with('success', 'Rekening berhasil ditambahkan.');
    }

    public function destroyAccount(Invitation $invitation, InvitationAccount $account): RedirectResponse
    {
        $account->delete();

        return back()->with('success', 'Rekening berhasil dihapus.');
    }

    public function updateGiftAddress(Request $request, Invitation $invitation): RedirectResponse
    {
        $validated = $request->validate([
            'receiver_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['required', 'string'],
        ]);

        $invitation->giftAddress()->updateOrCreate([], $validated);

        return back()->with('success', 'Alamat kado fisik berhasil disimpan.');
    }
}