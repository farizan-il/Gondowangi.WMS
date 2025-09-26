<?php

namespace App\Http\Controllers\Gondowangi\Kandidat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

use App\Models\Karyawan;
use App\Models\CareerPosition;
use App\Mail\KandidatLamaranMail;
use Illuminate\Support\Facades\Auth;

class KaryawanController extends Controller
{
    // public function dashboard()
    // {
    //     $user = Auth::user();
        
    //     // Ambil semua data lamaran dari kandidat ini
    //     $lamaranList = Karyawan::where('user_id', $user->id)
    //         ->with('posisilamaran') // Eager loading relasi posisi
    //         ->orderBy('created_at', 'desc')
    //         ->get();
    //     // dd($lamaranList->toArray());
    //     if ($lamaranList->isEmpty()) {
    //         // Jika belum pernah mengisi form, redirect ke form pengisian
    //         return redirect()->route('karyawan.index');
    //     }
        
    //     // Ambil lamaran terbaru sebagai data utama
    //     $karyawan = $lamaranList->first();
        
    //     return view('Gondowangi.Kandidat.User.dashboardkandidat', compact('karyawan', 'lamaranList'));
    // }
    
    public function dashboard()
    {
        $user = Auth::user();
        
        // Ambil semua data lamaran dari kandidat ini yang posisinya belum lewat tenggat waktu
        $lamaranList = Karyawan::where('user_id', $user->id)
            ->with(['posisilamaran' => function($query) {
                $query->notExpired(); // Menggunakan scope yang sudah ada
            }])
            ->whereHas('posisilamaran', function($query) {
                $query->notExpired(); // Menggunakan scope yang sudah ada
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        // dd($lamaranList->toArray());
        
        if ($lamaranList->isEmpty()) {
            // Jika belum pernah mengisi form atau semua lamaran sudah expired, redirect ke form pengisian
            return redirect()->route('karyawan.index');
        }
        
        // Ambil lamaran terbaru sebagai data utama
        $karyawan = $lamaranList->first();
        
        return view('Gondowangi.Kandidat.User.dashboardkandidat', compact('karyawan', 'lamaranList'));
    }
    
    // Method untuk melihat detail lamaran tertentu
    public function viewApplication($id)
    {
        $user = Auth::user();
        
        // Pastikan lamaran ini milik user yang sedang login
        $karyawan = Karyawan::where('id', $id)
                           ->where('user_id', $user->id)
                           ->firstOrFail();
        
        // Ambil semua lamaran untuk sidebar
        $lamaranList = Karyawan::where('user_id', $user->id)
                              ->orderBy('created_at', 'desc')
                              ->get();
        
        return view('Gondowangi.Kandidat.User.dashboardkandidat', compact('karyawan', 'lamaranList'));
    }
    
    // Tampilkan form pengisian kandidat
    public function index($karyawan_id = null)
    {
        $user = Auth::user();
    
        $posisiSudahDilamar = Karyawan::where('user_id', $user->id)
            ->pluck('posisi_dilamar_id')
            ->toArray();
    
        $availablePositions = CareerPosition::where('status', 'open')
            ->whereNotIn('id', $posisiSudahDilamar)
            ->get();
    
        $prefill = $karyawan_id
            ? Karyawan::where('user_id', $user->id)->where('id', $karyawan_id)->first()
            : Karyawan::where('user_id', $user->id)->latest()->first();
    
        $pendidikan_json = $prefill?->pendidikan_formal;
        $pengalaman_json = $prefill?->pengalaman_kerja;
        
        // Tangani jika sudah array (misal karena casting model), jika belum decode JSON
        $pendidikanFormal = is_string($pendidikan_json) ? json_decode($pendidikan_json, true) : ($pendidikan_json ?? []);
        $pengalamanKerja = is_string($pengalaman_json) ? json_decode($pengalaman_json, true) : ($pengalaman_json ?? []);
        
        return view('Gondowangi.Kandidat.User.formkandidat', compact(
            'availablePositions', 'prefill', 'pendidikanFormal', 'pengalamanKerja'
        ));
    }
    
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'posisi_dilamar_id' => 'required|exists:career_positions,id',
    //         'nama' => 'required|string|max:255',
    //         'tanggal_lahir' => 'required|string',
    //         'kota_domisili' => 'required|string',
    //         'no_telepon' => 'required|string',
    //         'email' => 'required|string',
    //         'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    //         'cv' => 'required|file|mimes:pdf,doc,docx|max:5120',
            
    //         // Validasi untuk data yang sebelumnya tidak ada
    //         'gaji_terakhir' => 'nullable|numeric',
    //         'tunjangan_terakhir' => 'nullable|string',
    //         'fasilitas_terakhir' => 'nullable|string',
    //         'fasilitas_lain' => 'nullable|string',
            
    //         'jabatan_diminati' => 'nullable|string',
    //         'gaji_diharapkan' => 'nullable|numeric',
    //         'tunjangan_diharapkan' => 'nullable|string',
    //         'fasilitas_diharapkan' => 'nullable|string',
    //         'jaminan_diharapkan' => 'nullable|string',
    //         'lain_diharapkan' => 'nullable|string',
            
    //         'informasi_tambahan' => 'nullable|string',
            
    //         // Validasi untuk array fields
    //         'pendidikan_formal' => 'nullable|array',
    //         'pendidikan_formal.*.jenjang' => 'nullable|string',
    //         'pendidikan_formal.*.nama_sekolah' => 'nullable|string',
    //         'pendidikan_formal.*.tahun_masuk' => 'nullable|integer',
    //         'pendidikan_formal.*.tahun_keluar' => 'nullable|integer',
    //         'pendidikan_formal.*.nilai' => 'nullable|string',
            
    //         'pengalaman_kerja' => 'required|array',
    //         'pengalaman_kerja.*.nama_perusahaan' => 'nullable|string',
    //         'pengalaman_kerja.*.jabatan' => 'nullable|string',
    //         'pengalaman_kerja.*.masa_kerja_dari' => 'nullable|date',
    //         'pengalaman_kerja.*.masa_kerja_sampai' => 'nullable|date',
    //         'pengalaman_kerja.*.masih_bekerja' => 'nullable|boolean',
    //         'pengalaman_kerja.*.uraian_pekerjaan' => 'nullable|string',
    //         'pengalaman_kerja.*.alasan_berhenti' => 'nullable|string',
    //     ]);
        
    //     $user = Auth::user();
    
    //     // Buat folder penyimpanan berdasarkan nama kandidat
    //     $folderName = str_replace(' ', '_', strtolower($validated['nama']));
    //     $storagePath = 'assets/kandidat/lamaran/' . $folderName;
        
    //     // Buat direktori jika belum ada
    //     if (!file_exists(public_path($storagePath))) {
    //         mkdir(public_path($storagePath), 0755, true);
    //     }
    
    //     // Handle upload foto
    //     $fotoPath = null;
    //     if ($request->hasFile('foto')) {
    //         $fotoFile = $request->file('foto');
    //         $fotoName = 'foto_' . time() . '.' . $fotoFile->getClientOriginalExtension();
    //         $fotoFile->move(public_path($storagePath), $fotoName);
    //         $fotoPath = $storagePath . '/' . $fotoName;
    //     }
    
    //     // Handle upload CV
    //     $cvPath = null;
    //     if ($request->hasFile('cv')) {
    //         $cvFile = $request->file('cv');
    //         $cvName = 'cv_' . time() . '.' . $cvFile->getClientOriginalExtension();
    //         $cvFile->move(public_path($storagePath), $cvName);
    //         $cvPath = $storagePath . '/' . $cvName;
    //     }
    
    //     // Prepare data untuk disimpan
    //     $dataToSave = [
    //         'user_id' => $user->id,
    //         'posisi_dilamar_id' => $validated['posisi_dilamar_id'],
    //         'nama' => $validated['nama'],
    //         'email' => $validated['email'],
    //         'kota_domisili' => $validated['kota_domisili'],
    //         'tanggal_lahir' => $validated['tanggal_lahir'],
    //         'no_telepon' => $validated['no_telepon'],
    //         'cv' => $cvPath,
    //         'foto' => $fotoPath,
    //         'status' => 'Pending',
            
    //         // Data informasi pekerjaan - yang diterima dari perusahaan terakhir
    //         'gaji_terakhir' => $validated['gaji_terakhir'] ?? null,
    //         'tunjangan_terakhir' => $validated['tunjangan_terakhir'] ?? null,
    //         'fasilitas_terakhir' => $validated['fasilitas_terakhir'] ?? null,
    //         'fasilitas_lain' => $validated['fasilitas_lain'] ?? null,
            
    //         // Data informasi pekerjaan - yang berhubungan dengan lamaran
    //         'jabatan_diminati' => $validated['jabatan_diminati'] ?? null,
    //         'gaji_diharapkan' => $validated['gaji_diharapkan'],
    //         'tunjangan_diharapkan' => $validated['tunjangan_diharapkan'] ?? null,
    //         'fasilitas_diharapkan' => $validated['fasilitas_diharapkan'] ?? null,
    //         'jaminan_diharapkan' => $validated['jaminan_diharapkan'] ?? null,
    //         'lain_diharapkan' => $validated['lain_diharapkan'] ?? null,
            
    //         // Informasi tambahan
    //         'informasi_tambahan' => $validated['informasi_tambahan'] ?? null,
            
    //         // Array fields - pastikan data yang kosong tidak disimpan
    //         'pendidikan_formal' => $this->cleanArrayData($validated['pendidikan_formal'] ?? []),
    //         'pengalaman_kerja' => $this->cleanArrayData($validated['pengalaman_kerja'] ?? []),
    //     ];
        
    //     Karyawan::create($dataToSave);
        
    //     return redirect()->route('kandidat.dashboard')
    //                     ->with('success', 'Data lamaran berhasil disimpan!');
    // }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'posisi_dilamar_id' => 'required|exists:career_positions,id',
            'nama' => 'required|string|max:255',
            'tanggal_lahir' => 'required|string',
            'kota_domisili' => 'required|string',
            'no_telepon' => 'required|string',
            'email' => 'required|string',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:5120',
            
            // Validasi untuk data yang sebelumnya tidak ada
            'gaji_terakhir' => 'nullable|numeric',
            'tunjangan_terakhir' => 'nullable|string',
            'fasilitas_terakhir' => 'nullable|string',
            'fasilitas_lain' => 'nullable|string',
            
            'jabatan_diminati' => 'nullable|string',
            'gaji_diharapkan' => 'nullable|numeric',
            'tunjangan_diharapkan' => 'nullable|string',
            'fasilitas_diharapkan' => 'nullable|string',
            'jaminan_diharapkan' => 'nullable|string',
            'lain_diharapkan' => 'nullable|string',
            
            'informasi_tambahan' => 'nullable|string',
            
            // Validasi untuk array fields
            'pendidikan_formal' => 'nullable|array',
            'pendidikan_formal.*.jenjang' => 'nullable|string',
            'pendidikan_formal.*.nama_sekolah' => 'nullable|string',
            'pendidikan_formal.*.tahun_masuk' => 'nullable|integer',
            'pendidikan_formal.*.tahun_keluar' => 'nullable|integer',
            'pendidikan_formal.*.nilai' => 'nullable|string',
            
            'pengalaman_kerja' => 'required|array',
            'pengalaman_kerja.*.nama_perusahaan' => 'nullable|string',
            'pengalaman_kerja.*.jabatan' => 'nullable|string',
            'pengalaman_kerja.*.masa_kerja_dari' => 'nullable|date',
            'pengalaman_kerja.*.masa_kerja_sampai' => 'nullable|date',
            'pengalaman_kerja.*.masih_bekerja' => 'nullable|boolean',
            'pengalaman_kerja.*.uraian_pekerjaan' => 'nullable|string',
            'pengalaman_kerja.*.alasan_berhenti' => 'nullable|string',
        ]);
        
        $user = Auth::user();
    
        // Buat folder penyimpanan berdasarkan nama kandidat
        $folderName = str_replace(' ', '_', strtolower($validated['nama']));
        $storagePath = 'assets/kandidat/lamaran/' . $folderName;
        
        // Buat direktori jika belum ada
        if (!file_exists(public_path($storagePath))) {
            mkdir(public_path($storagePath), 0755, true);
        }
    
        // Handle upload foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoFile = $request->file('foto');
            $fotoName = 'foto_' . time() . '.' . $fotoFile->getClientOriginalExtension();
            $fotoFile->move(public_path($storagePath), $fotoName);
            $fotoPath = $storagePath . '/' . $fotoName;
        }
    
        // Handle upload CV
        $cvPath = null;
        if ($request->hasFile('cv')) {
            $cvFile = $request->file('cv');
            $cvName = 'cv_' . time() . '.' . $cvFile->getClientOriginalExtension();
            $cvFile->move(public_path($storagePath), $cvName);
            $cvPath = $storagePath . '/' . $cvName;
        }
    
        // Prepare data untuk disimpan
        $dataToSave = [
            'user_id' => $user->id,
            'posisi_dilamar_id' => $validated['posisi_dilamar_id'],
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'kota_domisili' => $validated['kota_domisili'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'no_telepon' => $validated['no_telepon'],
            'cv' => $cvPath,
            'foto' => $fotoPath,
            'status' => 'Pending',
            
            // Data informasi pekerjaan - yang diterima dari perusahaan terakhir
            'gaji_terakhir' => $validated['gaji_terakhir'] ?? null,
            'tunjangan_terakhir' => $validated['tunjangan_terakhir'] ?? null,
            'fasilitas_terakhir' => $validated['fasilitas_terakhir'] ?? null,
            'fasilitas_lain' => $validated['fasilitas_lain'] ?? null,
            
            // Data informasi pekerjaan - yang berhubungan dengan lamaran
            'jabatan_diminati' => $validated['jabatan_diminati'] ?? null,
            'gaji_diharapkan' => $validated['gaji_diharapkan'],
            'tunjangan_diharapkan' => $validated['tunjangan_diharapkan'] ?? null,
            'fasilitas_diharapkan' => $validated['fasilitas_diharapkan'] ?? null,
            'jaminan_diharapkan' => $validated['jaminan_diharapkan'] ?? null,
            'lain_diharapkan' => $validated['lain_diharapkan'] ?? null,
            
            // Informasi tambahan
            'informasi_tambahan' => $validated['informasi_tambahan'] ?? null,
            
            // Array fields - pastikan data yang kosong tidak disimpan
            'pendidikan_formal' => $this->cleanArrayData($validated['pendidikan_formal'] ?? []),
            'pengalaman_kerja' => $this->cleanArrayData($validated['pengalaman_kerja'] ?? []),
        ];
        
        // Simpan data kandidat ke database
        $kandidat = Karyawan::create($dataToSave);
        
        // Kirim email notifikasi ke kandidat
        try {
            Mail::to($validated['email'])->send(new KandidatLamaranMail($kandidat));
        } catch (\Exception $e) {
            // Log error tapi jangan gagalkan proses simpan
            \Log::error('Failed to send email notification: ' . $e->getMessage());
        }
        
        return redirect()->route('kandidat.dashboard')
                        ->with('success', 'Data lamaran berhasil disimpan dan email konfirmasi telah dikirim!');
    }
   
    private function cleanArrayData($arrayData)
    {
        if (!is_array($arrayData)) {
            return [];
        }
        
        $cleanedData = [];
        foreach ($arrayData as $item) {
            // Cek apakah item memiliki data yang tidak kosong
            $hasData = false;
            foreach ($item as $value) {
                if (!empty($value)) {
                    $hasData = true;
                    break;
                }
            }
            
            // Hanya tambahkan item yang memiliki data
            if ($hasData) {
                $cleanedData[] = $item;
            }
        }
        
        return $cleanedData;
    }
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'posisi_dilamar_id' => 'required|exists:career_positions,id',
    //         'nama' => 'required|string|max:255',
    //         'tanggal_lahir' => 'required|string',
    //         'kota_domisili' => 'required|string',
    //         'no_telepon' => 'required|string',
    //         'email' => 'required|string',
    //         'informasi_tambahan' => 'required|string',
    //         'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    //         'cv' => 'required|file|mimes:pdf,doc,docx|max:5120',
    //     ]);
        
    //     $user = Auth::user();
    
    //     // Buat folder penyimpanan berdasarkan nama kandidat
    //     $folderName = str_replace(' ', '_', strtolower($validated['nama']));
    //     $storagePath = 'assets/kandidat/lamaran/' . $folderName;
        
    //     // Buat direktori jika belum ada
    //     if (!file_exists(public_path($storagePath))) {
    //         mkdir(public_path($storagePath), 0755, true);
    //     }
    
    //     // Handle upload foto
    //     $fotoPath = null;
    //     if ($request->hasFile('foto')) {
    //         $fotoFile = $request->file('foto');
    //         $fotoName = 'foto_' . time() . '.' . $fotoFile->getClientOriginalExtension();
    //         $fotoFile->move(public_path($storagePath), $fotoName);
    //         $fotoPath = $storagePath . '/' . $fotoName;
    //     }
    
    //     // Handle upload CV
    //     $cvPath = null;
    //     if ($request->hasFile('cv')) {
    //         $cvFile = $request->file('cv');
    //         $cvName = 'cv_' . time() . '.' . $cvFile->getClientOriginalExtension();
    //         $cvFile->move(public_path($storagePath), $cvName);
    //         $cvPath = $storagePath . '/' . $cvName;
    //     }
    
    //     // Simpan data dengan status default 'pending'
    //     $validated['user_id'] = $user->id;
    //     $validated['status'] = 'Pending';
    //     $validated['foto'] = $fotoPath;
    //     $validated['cv'] = $cvPath;
        
    //     // Handle array fields
    //     $arrayFields = [
    //         'pendidikan_formal', 'pengalaman_kerja',
    //     ];
        
    //     foreach ($arrayFields as $field) {
    //         if ($request->has($field)) {
    //             $validated[$field] = $request->input($field);
    //         }
    //     }
        
    //     Karyawan::create($validated);
        
    //     return redirect()->route('kandidat.dashboard')
    //                     ->with('success', 'Data lamaran berhasil disimpan!');
    // }
    
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         // 'asal_daerah' => 'required|string',
    //         // 'user_id' => 'required|string',
    //         'posisi_dilamar_id' => 'required|exists:career_positions,id',
    //         'nama' => 'required|string|max:255',
    //         'tanggal_lahir' => 'required|string',
    //         'kota_domisili' => 'required|string',
    //         'no_telepon' => 'required|string',
    //         'email' => 'required|string',
    //         // 'pendidikan_formal' => 'required|string',
    //         // 'pengalaman_kerja' => 'required|string',
    //         'informasi_tambahan' => 'required|string',
    //         // 'foto' => 'required|string',
    //         // 'cv' => 'required|string',
    //     ]);
        
    //     $user = Auth::user();
        
    //     // Cek apakah sudah pernah mengisi form
    //     // $existingData = Karyawan::where('user_id', $user->id)->first();
        
    //     // if ($existingData) {
    //     //     return redirect()->route('kandidat.dashboard')
    //     //         ->with('error', 'Anda sudah mengisi form lamaran sebelumnya.');
    //     // }
        
    //     // Simpan data dengan status default 'pending'
    //     $validated['user_id'] = $user->id;
    //     $validated['status'] = 'Pending';
        
    //     // Handle array fields
    //     $arrayFields = [
    //         'pendidikan_formal', 'pengalaman_kerja',
    //     ];
        
    //     foreach ($arrayFields as $field) {
    //         if ($request->has($field)) {
    //             $validated[$field] = $request->input($field);
    //         }
    //     }
        
    //     Karyawan::create($validated);
        
    //     return redirect()->route('kandidat.dashboard')
    //                     ->with('success', 'Data lamaran berhasil disimpan!');
    // }
    
    // Simpan data kandidat
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         // 'asal_daerah' => 'required|string',
    //         'posisi_dilamar_id' => 'required|exists:career_positions,id',
    //         'nama' => 'required|string|max:255',
    //         'alamat_ktp' => 'required|string',
    //         'user_id' => 'required|string',
    //         'email' => 'required|string',
    //         'kota_domisili' => 'required|string',
    //         'tanggal_lahir' => 'required|string',
    //         'no_telepon' => 'required|string',
    //         'riwayat_pendidikan' => 'required|string',
    //         'riwayat_pekerjaan' => 'required|string',
    //         'gaji_terakhir' => 'required|string',
    //         'gaji_diharapkan' => 'required|string',
    //         'foto' => 'required|string',
    //         'cv' => 'required|string',
            
            
            
    //         // 'asal_daerah' => 'required|string',
    //         // 'posisi_dilamar_id' => 'required|exists:career_positions,id',
    //         // 'nama_lengkap' => 'required|string|max:255',
    //         // 'alamat_ktp' => 'required|string',
    //         // 'alamat_ktp' => 'required|string',
    //         // 'tempat_lahir' => 'required|string|max:255',
    //         // 'tanggal_lahir' => 'required|date',
    //         // 'alamat_ktp' => 'required|string',
    //         // 'nama_ayah'  => 'required|string',
    //         // 'tanggal_lahir_ayah'  => 'required|string',
    //         // 'nama_pasangan'  => 'required|string',
    //         // 'jumlah_anak'  => 'required|string',
    //         // 'riwayat_penyakit'  => 'required|string',
    //         // 'facebook'  => 'required|string',
    //         // 'linkedin'  => 'required|string',
    //         // 'tiktok'  => 'required|string',
    //         // 'twitter'  => 'required|string',
    //         // 'instagram'  => 'required|string',
    //         // 'medsos_lain'  => 'required|string',
    //         // 'pekerjaan_ayah'  => 'required|string',
    //         // 'pendidikan_ayah'  => 'required|string',
    //         // 'nama_ibu'  => 'required|string',
    //         // 'pekerjaan_ibu'  => 'required|string',
    //         // 'pendidikan_ibu'  => 'required|string',
    //         // 'tanggal_lahir_ibu'  => 'required|string',
    //         // 'bahasa_inggris'  => 'required|string',
    //         // 'bahasa_asing_lain'  => 'required|string',
    //         // 'kemampuan_komputer'  => 'required|string',
    //         // 'keterampilan_lain'  => 'required|string',
    //         // 'kegiatan_waktu_luang'  => 'required|string',
    //         // 'hobi'  => 'required|string',
    //         // 'prestasi_karya'  => 'required|string',
    //         // 'gaji_terakhir'  => 'required|string',
    //         // 'tunjangan_terakhir'  => 'required|string',
    //         // 'fasilitas_terakhir'  => 'required|string',
    //         // 'fasilitas_lain'  => 'required|string',
    //         // 'bpjs_tk'  => 'required|string',
    //         // 'bpjs_kesehatan'  => 'required|string',
    //         // 'npwp'  => 'required|string',
    //         // 'bidang_pekerjaan_diminati'  => 'required|string',
    //         // 'jabatan_diminati'  => 'required|string',
    //         // 'tunjangan_diharapkan'  => 'required|string',
    //         // 'fasilitas_diharapkan'  => 'required|string',
    //         // 'jaminan_diharapkan'  => 'required|string',
    //         // 'lain_diharapkan'  => 'required|string',
    //         // 'informasi_tambahan'  => 'required|string',
    //         // 'alamat_tinggal' => 'required|string',
    //         // 'no_telepon' => 'required|string|max:20',
    //         // 'email' => 'required|email|max:255',
    //         // 'agama' => 'required|string|max:50',
    //         // 'tinggi_badan' => 'required|numeric',
    //         // 'berat_badan' => 'required|numeric',
    //         // 'status_pernikahan' => 'required|string|max:50',
    //         // 'golongan_darah' => 'required',
    //         // 'bahasa_inggris' => 'required',
    //         // 'gaji_diharapkan'  => 'required',
    //         // 'kesediaan_medical_checkup'  => 'required',
    //         // 'kesediaan_psikologi'  => 'required',
    //         // 'kesediaan_masa_percobaan' => 'required',
    //         // 'kesediaan_perjalanan_dinas'  => 'required',
    //         // 'kesediaan_pindah_kota' => 'required',
    //         // 'kapan_mulai_kerja' => 'required',
    //         // tambahkan validasi lainnya sesuai kebutuhan
    //     ]);
        
        
        
    //     $user = Auth::user();
        
    //     // Cek apakah sudah pernah mengisi form
    //     $existingData = Karyawan::where('user_id', $user->id)->first();
        
    //     if ($existingData) {
    //         return redirect()->route('kandidat.dashboard')
    //                       ->with('error', 'Anda sudah mengisi form lamaran sebelumnya.');
    //     }
        
    //     // Simpan data dengan status default 'pending'
    //     $validated['user_id'] = $user->id;
    //     $validated['status'] = 'pending';
        
    //     // Handle array fields
    //     $arrayFields = [
    //         'data_saudara', 'data_anak', 'pendidikan_formal', 
    //         'pendidikan_non_formal', 'pengalaman_kerja', 
    //         'aktivitas_sosial', 'referensi', 'kontak_darurat', 
    //         'sim', 'kesediaan_penempatan'
    //     ];
        
    //     foreach ($arrayFields as $field) {
    //         if ($request->has($field)) {
    //             $validated[$field] = $request->input($field);
    //         }
    //     }
        
    //     Karyawan::create($validated);
        
    //     return redirect()->route('kandidat.dashboard')
    //                     ->with('success', 'Data lamaran berhasil disimpan!');
    // }
    
    // Update data kandidat (jika diperlukan)
    public function update(Request $request)
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        
        if (!$karyawan) {
            return redirect()->route('karyawan.index');
        }
        
        // Hanya bisa update jika status masih pending atau save
        if (!in_array($karyawan->status, ['pending', 'save'])) {
            return redirect()->route('kandidat.dashboard')
                           ->with('error', 'Data tidak dapat diubah karena sudah diproses.');
        }
        
        // Validasi dan update data
        $validated = $request->validate([
            // tambahkan validasi sesuai kebutuhan
        ]);
        
        $karyawan->update($validated);
        
        return redirect()->route('kandidat.dashboard')
                        ->with('success', 'Data berhasil diperbarui!');
    }
    
    // Download atau view dokumen (jika ada)
    public function downloadDocument($type)
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        
        if (!$karyawan) {
            abort(404);
        }
    }
}