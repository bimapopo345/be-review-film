<?php

namespace App\Http\Controllers;

use App\Models\Cast;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CastController extends Controller
{
    public function index()
    {
        $data = Cast::all();
        return response()->json([
            'message' => 'tampil data berhasil',
            'data' => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'bio' => 'required|string',
            'age' => 'required|integer'
        ]);
        Cast::create([
            'id' => (string) Str::uuid(),
            'name' => $request->name,
            'bio' => $request->bio,
            'age' => $request->age
        ]);
        return response()->json([
            'message' => 'Tambah Cast berhasil'
        ], 201);
    }

    public function show($id)
    {
        $cast = Cast::find($id);
        if (!$cast) {
            return response()->json(['message' => 'Cast tidak ditemukan'], 404);
        }
        return response()->json([
            'message' => 'Detail Data Cast',
            'data' => $cast
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $cast = Cast::find($id);
        if (!$cast) {
            return response()->json(['message' => 'Cast tidak ditemukan'], 404);
        }
        $request->validate([
            'name' => 'required|string',
            'bio' => 'required|string',
            'age' => 'required|integer'
        ]);
        $cast->update([
            'name' => $request->name,
            'bio' => $request->bio,
            'age' => $request->age
        ]);
        return response()->json([
            'message' => 'Update Cast berhasil'
        ], 200);
    }

    public function destroy($id)
    {
        $cast = Cast::find($id);
        if (!$cast) {
            return response()->json(['message' => 'Cast tidak ditemukan'], 404);
        }
        $cast->delete();
        return response()->json([
            'message' => 'berhasil Menghapus Cast'
        ], 200);
    }
}
