<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile.
     */
    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'npm_nik' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:255',
        ]);

        $user->update($validatedData);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user,
        ]);
    }
}