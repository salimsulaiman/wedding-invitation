<?php

namespace App\Http\Middleware;

use App\Models\Invitation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInvitationOwnership
{
    public function handle(Request $request, Closure $next): Response
    {
        $invitation = $request->route('invitation');

        if ($invitation instanceof Invitation) {
            $user = $request->user();

            $isOwner = $invitation->user_id === $user->id;
            $isAdmin = $user->isAdmin();

            if (! $isOwner && ! $isAdmin) {
                abort(403, 'Anda tidak memiliki akses ke undangan ini.');
            }
        }

        return $next($request);
    }
}