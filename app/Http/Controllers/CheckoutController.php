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

        if (empty($cartIds)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $courses = Course::whereIn('id', $cartIds)->get();
        $total = $courses->sum('price');

        return view('checkout.index', compact('courses', 'total'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'transaction_number' => ['required', 'string', 'min:6'],
            'course_id'          => ['required', 'exists:courses,id'],
        ]);

        $course = Course::findOrFail($request->course_id);

        $order = Order::create([
            'user_id'            => Auth::id(),
            'course_id'          => $course->id,
            'transaction_number' => $request->transaction_number,
            'amount'             => $course->price,
            'status'             => 'pending',
        ]);

        $cart = session()->get('cart', []);
        $cart = array_filter($cart, fn($id) => $id !== (string) $course->id);
        session()->put('cart', array_values($cart));

        return redirect()->route('checkout.confirmation', $order->id);
    }

    public function confirmation(Order $order)
    {
        $order->load('course', 'user');
        return view('checkout.confirmation', compact('order'));
    }
}
