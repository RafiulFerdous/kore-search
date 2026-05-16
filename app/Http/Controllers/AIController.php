<?php

namespace App\Http\Controllers;

use App\Services\AI\Contracts\AIService;
use Illuminate\Http\Request;

class AIController extends Controller
{
    public function suggestCourse(Request $request, AIService $ai)
    {
        $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $details = $ai->suggestDetails($request->title, $request->category);
            return response()->json($details);
        } catch (\Exception $e) {
            return response()->json(['error' => 'AI generation failed. Please try again.'], 500);
        }
    }
}
