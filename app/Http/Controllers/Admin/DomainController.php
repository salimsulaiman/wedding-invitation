<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\DomainHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DomainController extends Controller
{
    public function index(Request $request): Response
    {
        $domains = Domain::query()
            ->with('invitation:id,name,user_id,is_active,expired_at')
            ->with('invitation.user:id,name')
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Domains/Index', [
            'domains' => $domains,
            'filters' => $request->only('search', 'status'),
        ]);
    }

    public function toggle(Domain $domain): RedirectResponse
    {
        $domain->is_active = ! $domain->is_active;
        $domain->activated_at = $domain->is_active ? now() : $domain->activated_at;
        $domain->deactivated_at = ! $domain->is_active ? now() : null;
        $domain->save();

        DomainHistory::create([
            'invitation_id' => $domain->invitation_id,
            'domain_id' => $domain->id,
            'old_name' => $domain->name,
            'new_name' => $domain->name,
            'changed_by' => auth()->id(),
            'note' => $domain->is_active ? 'Domain diaktifkan kembali oleh admin' : 'Domain dinonaktifkan oleh admin',
        ]);

        return back()->with('success', $domain->is_active ? 'Domain diaktifkan.' : 'Domain dinonaktifkan.');
    }
}