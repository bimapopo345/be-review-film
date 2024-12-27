<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'critic' => 'required',
            'rating' => 'required|integer|between:1,5',
            'movie_id' => 'required|uuid|exists:movie,id'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $existingReview = Review::where('movie_id', $request->movie_id)
            ->where('user_id', $user->id)
            ->first();
        if ($existingReview) {
            $existingReview->critic = $request->critic;
            $existingReview->rating = $request->rating;
            $existingReview->save();
            return response()->json([
                'message' => 'Review berhasil dibuat/diubah',
                'data' => $existingReview
            ], 200);
        } else {
            $review = Review::create([
                'id' => (string) Str::uuid(),
                'critic' => $request->critic,
                'rating' => $request->rating,
                'movie_id' => $request->movie_id,
                'user_id' => $user->id
            ]);
            return response()->json([
                'message' => 'Review berhasil dibuat/diubah',
                'data' => $review
            ], 200);
        }
    }
}
