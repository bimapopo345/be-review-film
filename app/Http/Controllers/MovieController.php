<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::all();
        return response()->json([
            'message' => 'tampil data berhasil',
            'data' => $movies
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'required|string',
            'year' => 'required|string|size:4',
            'poster' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'genre_id' => 'required|uuid|exists:genres,id',
        ]);
        $uploadedFile = $request->file('poster');
        $uploadResult = Cloudinary::upload($uploadedFile->getRealPath(), ['folder' => 'uploads']);
        $posterUrl = $uploadResult->getSecurePath();

        Movie::create([
            'id' => (string) Str::uuid(),
            'title' => $request->title,
            'summary' => $request->summary,
            'year' => $request->year,
            'poster' => $posterUrl,
            'genre_id' => $request->genre_id,
        ]);
        return response()->json([
            'message' => 'Tambah Movie berhasil'
        ], 201);
    }

    public function show($id)
    {
        $movie = Movie::find($id);
        if (!$movie) {
            return response()->json(['message' => 'Movie tidak ditemukan'], 404);
        }
        return response()->json([
            'message' => 'Detail Data Movie',
            'data' => $movie
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $movie = Movie::find($id);
        if (!$movie) {
            return response()->json(['message' => 'Movie tidak ditemukan'], 404);
        }
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'summary' => 'sometimes|required|string',
            'year' => 'sometimes|required|string|size:4',
            'poster' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'genre_id' => 'sometimes|required|uuid|exists:genres,id',
        ]);
        $data = $request->only(['title', 'summary', 'year', 'genre_id']);
        if ($request->hasFile('poster')) {
            if ($movie->poster) {
                $publicId = explode('/', parse_url($movie->poster, PHP_URL_PATH))[2];
                $publicId = explode('.', $publicId)[0];
                Cloudinary::destroy('uploads/'.$publicId);
            }
            $uploadedFile = $request->file('poster');
            $uploadResult = Cloudinary::upload($uploadedFile->getRealPath(), ['folder' => 'uploads']);
            $posterUrl = $uploadResult->getSecurePath();
            $data['poster'] = $posterUrl;
        }
        $movie->update($data);
        return response()->json([
            'message' => 'Update Movie berhasil'
        ], 200);
    }

    public function destroy($id)
    {
        $movie = Movie::find($id);
        if (!$movie) {
            return response()->json(['message' => 'Movie tidak ditemukan'], 404);
        }
        if ($movie->poster) {
            $publicId = explode('/', parse_url($movie->poster, PHP_URL_PATH))[2];
            $publicId = explode('.', $publicId)[0];
            Cloudinary::destroy('uploads/'.$publicId);
        }
        $movie->delete();
        return response()->json([
            'message' => 'berhasil Menghapus Movie'
        ], 200);
    }
}
