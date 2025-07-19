<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna.
     * Mengambil pengguna selain admin yang sedang login untuk mencegah admin mengedit dirinya sendiri.
     */
    public function index()
    {
        // Ambil semua pengguna kecuali admin yang sedang login
        $users = User::where('id', '!=', auth()->id())
                     ->orderBy('name', 'asc')
                     ->paginate(10); // Menggunakan paginasi untuk data yang banyak

        return response()->json($users);
    }

    /**
     * Menampilkan detail satu pengguna.
     * (Opsional, tapi baik untuk dimiliki)
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

    /**
     * Mengupdate data pengguna.
     * Dalam konteks ini, admin hanya bisa mengubah peran (role) pengguna.
     */
    public function update(Request $request, User $user)
    {
        // Mencegah admin mengubah datanya sendiri melalui endpoint ini
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Admin cannot change their own role.'], 403);
        }

        $validatedData = $request->validate([
            'role' => ['required', Rule::in(['admin', 'user'])],
        ]);

        $user->update($validatedData);

        return response()->json([
            'message' => 'User role has been updated successfully.',
            'user' => $user,
        ]);
    }

    /**
     * Menghapus pengguna dari sistem.
     */
    public function destroy(User $user)
    {
        // Mencegah admin menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Admin cannot delete their own account.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User has been deleted successfully.'], 200);
    }
}