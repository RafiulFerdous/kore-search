<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartIds = session()->get('cart', []);
        $cartPrices = session()->get('cart_prices', []);
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

        return view('cart.index', compact('courses', 'total', 'priceChanges'));
    }

    public function add(Request $request, Course $course)
    {
        $user = $request->user();

        abort_unless($user && $user->isStudent(), 401, 'You must be logged in as a student.');

        if ($user->id === $course->instructor_id) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'You cannot add your own course to cart.'], 422)
                : back()->with('error', 'You cannot add your own course to cart.');
        }

        if ($course->isPurchasedBy($user)) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'You already own this course.'], 422)
                : back()->with('error', 'You already own this course.');
        }

        $cart = session()->get('cart', []);

        if (in_array($course->id, $cart)) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Course is already in your cart.'], 422)
                : back()->with('info', 'Course is already in your cart.');
        }

        $cart[] = $course->id;
        session()->put('cart', $cart);
        session()->put('cart_prices.' . $course->id, $course->price);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count'   => count($cart),
                'message' => 'Course added to cart.',
            ]);
        }

        return redirect()->back()->with('success', 'Course added to cart.');
    }

    public function remove(Request $request, Course $course)
    {
        $cart = session()->get('cart', []);
        $cart = array_values(array_filter($cart, fn($id) => (int) $id !== $course->id));
        session()->put('cart', $cart);
        session()->forget('cart_prices.' . $course->id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count'   => count($cart),
                'message' => 'Course removed from cart.',
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Course removed from cart.');
    }

    public function clearAll(Request $request)
    {
        session()->forget(['cart', 'cart_prices']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count'   => 0,
                'message' => 'Cart cleared.',
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Cart cleared.');
    }
}
