<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('User/Dashboard/Index', [
            'invitations' => $user->invitations()
                ->with('theme:id,name')
                ->withCount(['guests', 'wishes'])
                ->latest()
                ->get(['id', 'user_id', 'theme_id', 'name', 'status', 'is_active', 'expired_at', 'created_at']),

            'recentOrders' => $user->orders()
                ->with('theme:id,name')
                ->latest()
                ->take(5)
                ->get(['id', 'theme_id', 'price', 'status', 'ordered_at', 'created_at']),
        ]);
    }
}