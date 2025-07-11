<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest; // <-- Gunakan ini
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse; // <-- Gunakan ini untuk redirect

class EmailVerificationController extends Controller
{
    /**
     * Menandai email pengguna yang diautentikasi sebagai terverifikasi.
     * Ini adalah metode yang jauh lebih aman dan andal.
     */
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        // Cek apakah email sudah diverifikasi sebelumnya
        if ($request->user()->hasVerifiedEmail()) {
            return redirect(env('FRONTEND_URL') . '/verification-success?message=Email_already_verified');
        }

        // Verifikasi email dan picu event 'Verified'
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
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