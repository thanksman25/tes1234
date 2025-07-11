<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AllometricEquation;
use App\Models\FormulaSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FormulaController extends Controller
{
    /**
     * Mengambil semua rumus yang sudah disetujui (aktif).
     */
    public function index()
    {
        $equations = AllometricEquation::all();
        return response()->json($equations);
    }

    /**
     * Mengajukan rumus baru oleh pengguna.
     */
    public function submit(Request $request)
    {
        $validatedData = $request->validate([
            'formula_name' => 'required|string|max:255',
            'equation_template' => 'required|string',
            'reference' => 'required|string|max:255',
            'supporting_document' => 'required|file|mimes:pdf|max:2048', // Maks 2MB
        ]);

        $filePath = $request->file('supporting_document')->store('formula_submissions', 'public');

        FormulaSubmission::create([
            'user_id' => Auth::id(),
            'formula_name' => $validatedData['formula_name'],
            'equation_template' => $validatedData['equation_template'],
            'reference' => $validatedData['reference'],
            'supporting_document_path' => $filePath,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Formula submitted successfully for review.'], 201);
    }

    // --- METODE KHUSUS ADMIN ---

    /**
     * (Admin) Mengambil semua pengajuan rumus.
     */
    public function getSubmissions()
    {
        // Ambil pengajuan beserta data pengguna yang mengajukan
        $submissions = FormulaSubmission::with('user:id,name,email')->latest()->get();
        return response()->json($submissions);
    }

    /**
     * (Admin) Menyetujui sebuah pengajuan rumus.
     */
    public function approve(FormulaSubmission $submission)
    {
        if ($submission->status !== 'pending') {
            return response()->json(['message' => 'This submission has already been reviewed.'], 409);
        }

        // Buat record baru di tabel rumus aktif
        $equation = AllometricEquation::create([
            'name' => $submission->formula_name,
            'equation_template' => $submission->equation_template,
            'reference' => $submission->reference,
            'submission_id' => $submission->id,
        ]);

        // Update status pengajuan
        $submission->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return response()->json(['message' => 'Formula approved and is now active.', 'equation' => $equation]);
    }

    /**
     * (Admin) Menolak sebuah pengajuan rumus.
     */
    public function reject(Request $request, FormulaSubmission $submission)
    {
        $request->validate(['rejection_reason' => 'required|string|max:1000']);

        if ($submission->status !== 'pending') {
            return response()->json(['message' => 'This submission has already been reviewed.'], 409);
        }

        $submission->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return response()->json(['message' => 'Submission has been rejected.']);
    }
}