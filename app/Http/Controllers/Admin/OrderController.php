<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of customer orders for administrators.
     */
    public function index(): View
    {
        $orders = Order::with('user')
            ->with(['items.product'])
            ->latest()
            ->paginate(12);

        $statusOptions = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];

        return view('admin.order', [
            'orders' => $orders,
            'statusOptions' => $statusOptions,
        ]);
    }

    /**
     * Update the order status from the admin interface.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,processing,shipped,delivered,cancelled'],
        ]);

        $order->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->back()
            ->with('success', "Status order {$order->order_number} berhasil diperbarui.");
    }
}
