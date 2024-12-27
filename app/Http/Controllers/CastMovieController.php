<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CastMovie;
use App\Models\Movie;
use App\Models\Cast;
use Illuminate\Support\Str;

class CastMovieController extends Controller
{
    public function index()
    {
        $data = CastMovie::all();
        return response()->json([
            'message' => 'Berhasil Tampil cast Movie',
            'data' => $data
        ], 200);
    }

    public function show($id)
    {
        $castMovie = CastMovie::with(['movie','cast'])->find($id);
        if (!$castMovie) {
            return response()->json(['message' => 'Cast Movie tidak ditemukan'], 404);
        }
        return response()->json([
            'message' => 'Berhasil Tampil cast Movie',
            'data' => $castMovie
        ], 200);
    }

    public function store(Request $request)
    {
        // Corrected Validation Rule: Changed 'movies' to 'movie'
        $request->validate([
            'name' => 'required|string|max:255',
            'cast_id' => 'required|uuid|exists:casts,id',
            'movie_id' => 'required|uuid|exists:movie,id', // Correct table name
        ]);

        // Create a new CastMovie entry
        $castMovie = CastMovie::create([
            'id' => Str::uuid(), // Generate a UUID for the primary key
            'name' => $request->name,
            'cast_id' => $request->cast_id,
            'movie_id' => $request->movie_id,
        ]);

        // Return a successful JSON response
        return response()->json([
            'message' => 'Berhasil tambah cast Movie',
            'data' => $castMovie
        ], 201); // 201 Created
    }
    public function update(Request $request, $id)
    {
        $castMovie = CastMovie::find($id);
        if (!$castMovie) {
            return response()->json(['message' => 'Cast Movie tidak ditemukan'], 404);
        }
        $request->validate([
            'name' => 'required|string',
            'cast_id' => 'required|uuid|exists:casts,id',
            'movie_id' => 'required|uuid|exists:movie,id',
        ]);
        $castMovie->update([
            'name' => $request->name,
            'cast_id' => $request->cast_id,
            'movie_id' => $request->movie_id
        ]);
        return response()->json([
            'message' => 'Berhasil Update cast Movie'
        ], 201);
    }

    public function destroy($id)
    {
        $castMovie = CastMovie::find($id);
        if (!$castMovie) {
            return response()->json(['message' => 'Cast Movie tidak ditemukan'], 404);
        }
        $castMovie->delete();
        return response()->json([
            'message' => 'Berhasil Delete cast Movie'
        ], 200);
    }
}
