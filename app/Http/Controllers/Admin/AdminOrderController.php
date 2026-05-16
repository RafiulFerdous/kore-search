<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'course'])->orderBy('created_at', 'desc')->paginate(10);

        return view('dashboard.admin.orders', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'in:pending,completed,failed'],
        ]);

        try {
            $order->update(['status' => $request->status]);
        } catch (\Throwable $e) {
            return redirect()->route('admin.orders')->with('error', 'Failed to update order status. Please try again.');
        }

        return redirect()->route('admin.orders')->with('success', "Order #{$order->id} status updated to {$request->status}.");
    }
}
