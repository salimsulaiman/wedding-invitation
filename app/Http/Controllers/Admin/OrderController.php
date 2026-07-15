<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ThemeCategory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = Order::query()
            ->with(['user:id,name,email', 'themeCategory:id,name'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->search, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$request->search}%")))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'customers' => User::where('role', 'user')->orderBy('name')->get(['id', 'name', 'email']),
            'themeCategories' => ThemeCategory::where('is_active', true)->orderBy('name')->get(['id', 'name', 'price']),
            'filters' => $request->only('search', 'status'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'theme_category_id' => ['nullable', 'exists:theme_categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'ordered_at' => ['required', 'date'],
        ]);

        $validated['status'] = 'pending';
        $validated['order_source'] = 'whatsapp';
        $validated['handled_by'] = auth()->id();

        Order::create($validated);

        return redirect()->route('admin.orders.index')->with('success', 'Pesanan berhasil dicatat.');
    }

    public function show(Order $order): Response
    {
        $order->load(['user', 'themeCategory', 'invitation:id,name,status', 'handledBy:id,name']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'paid', 'cancelled', 'completed'])],
            'price' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['handled_by'] = auth()->id();

        $order->update($validated);

        return back()->with('success', 'Pesanan berhasil diperbarui.');
    }
}