<?php

namespace App\Http\Controllers\Gondowangi\KontakKami;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Contact;

class KontakKamiController extends Controller
{
    public function index()
    {
        return view('Gondowangi.KontakKami.index', [
            'title' => 'KontakKami',
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'destination' => 'required|in:head_office,factory'
        ], [
            'fullName.required' => 'Nama lengkap harus diisi',
            'fullName.max' => 'Nama lengkap maksimal 255 karakter',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',
            'subject.max' => 'Subjek maksimal 255 karakter',
            'message.required' => 'Pesan harus diisi',
            'destination.required' => 'Tujuan harus dipilih',
            'destination.in' => 'Tujuan tidak valid'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Simpan data ke database
            $contact = Contact::create([
                'nama_lengkap' => $request->fullName,
                'alamat_email' => $request->email,
                'subjek' => $request->subject,
                'komentar_pesan' => $request->message,
                'untuk' => $request->destination,
                'status' => 'baru', // atau sesuai kebutuhan
                'is_read' => false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dikirim! Terima kasih atas masukan Anda.',
                'data' => $contact
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                // 'trace' => $e->getTrace(), // <- aktifkan ini jika butuh full stack trace
            ], 500);
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
