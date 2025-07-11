<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * Menandai email pengguna sebagai terverifikasi.
     * Logika ini dimodifikasi untuk tidak bergantung pada sesi login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request): RedirectResponse
    {
        // 1. Cari pengguna berdasarkan ID dari URL
        $user = User::find($request->route('id'));

        // Jika pengguna tidak ditemukan atau hash tidak valid, gagalkan.
        // Fungsi hash_equals() aman untuk perbandingan string.
        if (! $user || ! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
             // Redirect ke halaman gagal di frontend
            return redirect(env('FRONTEND_URL') . '/verification-failure?message=Invalid_verification_link');
        }

        // 2. Cek apakah email sudah diverifikasi sebelumnya
        if ($user->hasVerifiedEmail()) {
            return redirect(env('FRONTEND_URL') . '/verification-success?message=Email_already_verified');
        }

        // 3. Verifikasi email pengguna dan picu event 'Verified'
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // 4. Redirect ke halaman sukses di frontend
        return redirect(env('FRONTEND_URL') . '/verification-success');
    }

    /**
     * Mengirim ulang email verifikasi.
     * Metode ini memerlukan pengguna untuk login, jadi tidak ada perubahan di sini.
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