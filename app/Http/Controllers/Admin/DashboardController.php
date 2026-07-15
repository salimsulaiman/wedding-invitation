<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationWish;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard/Index', [
            'stats' => $this->stats(),
            'recentInvitations' => $this->recentInvitations(),
            'recentOrders' => $this->recentOrders(),
            'recentWishes' => $this->recentWishes(),
        ]);
    }

    private function stats(): array
    {
        return [
            'total_customers' => User::where('role', 'user')->count(),
            'total_invitations' => Invitation::count(),
            'active_invitations' => Invitation::where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('expired_at')
                        ->orWhere('expired_at', '>', now());
                })
                ->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'revenue_this_month' => (float) Order::where('status', 'paid')
                ->whereMonth('ordered_at', now()->month)
                ->whereYear('ordered_at', now()->year)
                ->sum('price'),
        ];
    }

    private function recentInvitations()
    {
        return Invitation::with(['user:id,name', 'theme:id,name'])
            ->latest()
            ->take(5)
            ->get(['id', 'user_id', 'theme_id', 'name', 'status', 'created_at']);
    }

    private function recentOrders()
    {
        return Order::with('user:id,name')
            ->latest()
            ->take(5)
            ->get(['id', 'user_id', 'price', 'status', 'ordered_at', 'created_at']);
    }

    private function recentWishes()
    {
        return InvitationWish::with('invitation:id,name')
            ->latest()
            ->take(5)
            ->get(['id', 'invitation_id', 'name', 'message', 'attendance', 'created_at']);
    }
}