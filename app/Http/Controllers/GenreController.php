<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GenreController extends Controller
{
    public function index()
    {
        $data = Genre::all();
        return response()->json([
            'message' => 'tampil data berhasil',
            'data' => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);
        Genre::create([
            'id' => (string) Str::uuid(),
            'name' => $request->name
        ]);
        return response()->json([
            'message' => 'Tambah Genre berhasil'
        ], 201);
    }

    public function show($id)
    {
        $genre = Genre::find($id);
        if (!$genre) {
            return response()->json(['message' => 'Genre tidak ditemukan'], 404);
        }
        return response()->json([
            'message' => 'Detail Data Genre',
            'data' => $genre
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $genre = Genre::find($id);
        if (!$genre) {
            return response()->json(['message' => 'Genre tidak ditemukan'], 404);
        }
        $request->validate([
            'name' => 'required|string'
        ]);
        $genre->update([
            'name' => $request->name
        ]);
        return response()->json([
            'message' => 'Update Genre berhasil'
        ], 200);
    }

    public function destroy($id)
    {
        $genre = Genre::find($id);
        if (!$genre) {
            return response()->json(['message' => 'Genre tidak ditemukan'], 404);
        }
        $genre->delete();
        return response()->json([
            'message' => 'berhasil Menghapus Genre'
        ], 200);
    }
}
