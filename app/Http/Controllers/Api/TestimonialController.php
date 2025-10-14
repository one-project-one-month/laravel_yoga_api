<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonial;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::with('user:id,name,email')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $testimonials
        ], 200);
    }
    public function store(Request $request)
    {
        $request->validate([
            'comment' => 'required|string|max:500',
        ]);

        // Create testimonial for the authenticated user
        $testimonial = Testimonial::create([
            'user_id' => $request->user()->id,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Testimonial created successfully',
            'data' => $testimonial->load('user:id,name,email'),
        ], 201);
    }
}
