<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartIds = session()->get('cart', []);
        $cartPrices = session()->get('cart_prices', []);

        if (empty($cartIds)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $courses = Course::with('instructor')->whereIn('id', $cartIds)->get()->keyBy('id');
        $courses = collect($cartIds)->map(fn($id) => $courses->get($id))->filter();

        $priceChanges = [];
        foreach ($courses as $course) {
            $snapshot = $cartPrices[$course->id] ?? null;
            if ($snapshot !== null && (float) $snapshot !== (float) $course->price) {
                $priceChanges[$course->id] = ['old' => $snapshot, 'new' => $course->price];
            }
        }

        $total = $courses->sum('price');

        return view('checkout.index', compact('courses', 'total', 'priceChanges'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'transaction_number' => ['required', 'string', 'min:6'],
        ]);

        $cartIds = session()->get('cart', []);

        if (empty($cartIds)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $courses = Course::whereIn('id', $cartIds)->get();
        $cartPrices = session()->get('cart_prices', []);

        foreach ($courses as $course) {
            $amount = $cartPrices[$course->id] ?? $course->price;

            Order::create([
                'user_id'            => Auth::id(),
                'course_id'          => $course->id,
                'transaction_number' => $request->transaction_number,
                'amount'             => $amount,
                'status'             => 'pending',
            ]);
        }

        session()->forget('cart');
        session()->forget('cart_prices');

        $firstOrder = Order::where('user_id', Auth::id())
            ->where('transaction_number', $request->transaction_number)
            ->latest()
            ->first();

        return redirect()->route('checkout.confirmation', $firstOrder->id);
    }

    public function confirmation(Order $order)
    {
        $order->load('course', 'user');

        $orders = Order::where('user_id', $order->user_id)
            ->where('transaction_number', $order->transaction_number)
            ->with('course')
            ->get();

        $totalAmount = $orders->sum('amount');

        return view('checkout.confirmation', compact('order', 'orders', 'totalAmount'));
    }
}
