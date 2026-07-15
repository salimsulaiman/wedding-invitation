<?php

namespace App\Http\Middleware;

use App\Models\Domain;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInvitationActiveStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $domainSlug = $request->route('domain');

        $domain = Domain::with('invitation')
            ->where('name', $domainSlug)
            ->first();

        if (! $domain || ! $domain->is_active) {
            abort(404, 'Undangan tidak ditemukan.');
        }

        $invitation = $domain->invitation;

        if (! $invitation || ! $invitation->is_active || $invitation->isExpired()) {
            abort(404, 'Undangan sudah tidak aktif atau telah kedaluwarsa.');
        }

        // Simpan supaya controller tidak perlu query ulang
        $request->attributes->set('invitation', $invitation);

        return $next($request);
    }
}