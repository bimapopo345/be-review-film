<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return response()->json([
            'message' => 'tampil data berhasil',
            'data' => $roles
        ]);
    }

    public function store(Request $request)
    {
        Role::create([
            'id' => (string) Str::uuid(),
            'name' => $request->name
        ]);
        return response()->json(['message' => 'Tambah Role berhasil']);
    }

    public function show($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }
        return response()->json([
            'message' => 'Detail Data Role',
            'data' => $role
        ]);
    }

    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }
        $role->update(['name' => $request->name]);
        return response()->json(['message' => 'Update Role berhasil']);
    }

    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }
        $role->delete();
        return response()->json(['message' => 'berhasil Menghapus Role']);
    }
}
