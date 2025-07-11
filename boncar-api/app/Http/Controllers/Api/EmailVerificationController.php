<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class EmailVerificationController extends Controller
{
    /**
     * Menandai email pengguna yang diautentikasi sebagai terverifikasi.
     * Ini adalah endpoint yang akan dituju oleh link di email.
     */
    public function verify(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        // Cek apakah hash URL valid
        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            // Redirect ke halaman gagal di frontend jika hash tidak cocok
            return redirect(env('FRONTEND_URL') . '/verification-failure?message=Invalid_verification_link');
        }

        // Cek apakah email sudah diverifikasi sebelumnya
        if ($user->hasVerifiedEmail()) {
            // Redirect ke halaman sukses dengan pesan bahwa sudah terverifikasi
            return redirect(env('FRONTEND_URL') . '/verification-success?message=Email_already_verified');
        }

        // Verifikasi email dan picu event 'Verified'
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Redirect ke halaman sukses di frontend
        return redirect(env('FRONTEND_URL') . '/verification-success');
    }

    /**
     * Mengirim ulang email verifikasi.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email sudah terverifikasi.'], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Link verifikasi baru telah dikirim.']);
    }
}
