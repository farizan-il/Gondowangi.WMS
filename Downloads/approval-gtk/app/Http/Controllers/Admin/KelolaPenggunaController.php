<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Department;
use App\Models\RoleLevel;
use App\Models\Golongan;
use App\Models\FlowApproval;
use App\Models\KategoriPengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class KelolaPenggunaController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::with(['department', 'roleLevel', 'atasan']);

        // Filter berdasarkan department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search berdasarkan nama atau email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $karyawans = $query->paginate(100)->withQueryString();
        
        // Data untuk dropdown filter
        $departments = Department::orderBy('nama')->get();
        $golongan = Golongan::get();
        $roleLevels = RoleLevel::orderBy('nama')->get();
        $atasanOptions = Karyawan::where('status', 'active')->orderBy('nama')->get();

        return view('Approval-app.HelpDesk.KelolaPengguna.index', compact(
            'karyawans', 
            'departments', 
            'roleLevels', 
            'atasanOptions',
            'golongan'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Method ini tidak digunakan karena menggunakan modal
    }

    /**
     * Store a newly created resource in storage.
     */
  

public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:Karyawan,email',
            'nik' => 'required|string|unique:Karyawan,nik|max:20',
            'department_id' => 'required|exists:Department,id',
            'role_level_id' => 'required|exists:RoleLevel,id',
            'golongan_id' => 'required|exists:Golongan,id',
        ], [
            'nama.required' => 'Nama lengkap harus diisi.',
            'nama.max' => 'Nama lengkap maksimal 255 karakter.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar, gunakan email lain.',
            'nik.required' => 'NIK harus diisi.',
            'nik.unique' => 'NIK sudah terdaftar, gunakan NIK lain.',
            'nik.max' => 'NIK maksimal 20 karakter.',
            'department_id.required' => 'Department harus dipilih.',
            'department_id.exists' => 'Department yang dipilih tidak valid.',
            'role_level_id.required' => 'Jabatan harus dipilih.',
            'role_level_id.exists' => 'Jabatan yang dipilih tidak valid.',
            'golongan_id.required' => 'Golongan harus dipilih.',
            'golongan_id.exists' => 'Golongan yang dipilih tidak valid.',
        ]);

        DB::beginTransaction();

        // Cek apakah email sudah ada (double check)
        if (Karyawan::where('email', $validated['email'])->exists()) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Email sudah terdaftar dalam sistem.',
                'errors' => ['email' => ['Email sudah terdaftar, gunakan email lain.']]
            ], 422);
        }

        // Cek apakah NIK sudah ada (double check)
        if (Karyawan::where('nik', $validated['nik'])->exists()) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'NIK sudah terdaftar dalam sistem.',
                'errors' => ['nik' => ['NIK sudah terdaftar, gunakan NIK lain.']]
            ], 422);
        }

        // Buat karyawan baru
        $karyawan = Karyawan::create([
            'nama' => trim($validated['nama']),
            'email' => strtolower(trim($validated['email'])),
            'nik' => trim($validated['nik']),
            'department_id' => $validated['department_id'],
            'role_level_id' => $validated['role_level_id'],
            'golongan_id' => $validated['golongan_id'],
            'status' => 'aktif',
            'password' => Hash::make('Gondowangi-123'),
        ]);

        // Log activity (opsional)
        \Log::info('Karyawan baru ditambahkan', [
            'karyawan_id' => $karyawan->id,
            'nama' => $karyawan->nama,
            'email' => $karyawan->email,
            'nik' => $karyawan->nik,
            'admin_id' => auth()->id() ?? 'system'
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Karyawan berhasil ditambahkan dengan password default: Gondowangi-123',
            'data' => [
                'id' => $karyawan->id,
                'nama' => $karyawan->nama,
                'email' => $karyawan->email,
                'nik' => $karyawan->nik
            ]
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollback();
        return response()->json([
            'success' => false,
            'message' => 'Data yang dimasukkan tidak valid.',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Illuminate\Database\QueryException $e) {
        DB::rollback();
        
        // Handle database constraint errors
        if (str_contains($e->getMessage(), 'Duplicate entry')) {
            if (str_contains($e->getMessage(), 'email')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email sudah terdaftar dalam sistem.',
                    'errors' => ['email' => ['Email sudah terdaftar, gunakan email lain.']]
                ], 422);
            }
            if (str_contains($e->getMessage(), 'nik')) {
                return response()->json([
                    'success' => false,
                    'message' => 'NIK sudah terdaftar dalam sistem.',
                    'errors' => ['nik' => ['NIK sudah terdaftar, gunakan NIK lain.']]
                ], 422);
            }
        }
        
        \Log::error('Database error saat menambah karyawan:', [
            'error' => $e->getMessage(),
            'data' => $validated ?? $request->all()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan database. Silakan coba lagi.'
        ], 500);
        
    } catch (\Exception $e) {
        DB::rollback();
        
        \Log::error('Error saat menambah karyawan:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'data' => $request->all()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.'
        ], 500);
    }
}


    /**
     * Display the specified resource.
     */
    public function show(Karyawan $karyawan)
    {
        $karyawan->load([
            'department', 
            'roleLevel', 
            'atasan', 
            'pengajuan' => function($query) {
                $query->with('kategoriPengajuan')->latest()->take(10);
            }
        ]);

        // Ambil otoritas pengajuan (FlowApproval dimana karyawan sebagai requester)
        $otoritasPengajuan = FlowApproval::where('requester_id', $karyawan->id)
            ->with(['kategoriPengajuan'])
            ->distinct('kategori_pengajuan_id')
            ->get()
            ->groupBy('kategori_pengajuan_id');

        // Ambil otoritas approval (FlowApproval dimana karyawan sebagai approver)
        $otoritasApproval = FlowApproval::where('approver_id', $karyawan->id)
            ->with(['kategoriPengajuan'])
            ->distinct('kategori_pengajuan_id')
            ->get()
            ->groupBy('kategori_pengajuan_id');

        return view('Approval-app.HelpDesk.KelolaPengguna.show', compact(
            'karyawan', 
            'otoritasPengajuan', 
            'otoritasApproval'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Karyawan $karyawan)
    {
        // Method ini tidak digunakan karena menggunakan modal
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Karyawan $karyawan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:Karyawan,email,' . $karyawan->id,
            'department_id' => 'required|exists:departments,id',
            'role_level_id' => 'required|exists:role_levels,id',
            'atasan_id' => 'nullable|exists:Karyawan,id',
            'status' => 'required|in:active,inactive',
            'phone' => 'nullable|string|max:20',
            'alamat' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $karyawan->update($validated);

            // Update user account jika ada
            if ($karyawan->user) {
                $karyawan->user->update([
                    'name' => $validated['nama'],
                    'email' => $validated['email']
                ]);
            }

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate karyawan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Karyawan $karyawan)
    {
        DB::beginTransaction();
        try {
            // Soft delete dengan mengubah status
            $karyawan->update(['status' => 'inactive']);
            
            // Nonaktifkan user account jika ada
            if ($karyawan->user) {
                $karyawan->user->delete(); // Soft delete
            }

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dinonaktifkan'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menonaktifkan karyawan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset password karyawan
     */
    public function resetPassword(Karyawan $karyawan)
    {
        try {
            if ($karyawan->user) {
                $karyawan->user->update([
                    'password' => Hash::make('Gondowangi-123')
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Password berhasil direset ke default'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'User account tidak ditemukan'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal reset password: ' . $e->getMessage()
            ], 500);
        }
    }
}