<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriPengajuan;
use App\Models\FlowApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KategoriPengajuanController extends Controller
{
    public function showPage()
    {
        return view('Approval-app.HelpDesk.KelolaFormPengajuan.Kategori');
    }

    public function index()
    {
        try {
            $kategori = KategoriPengajuan::orderBy('nama')->get();
            
            return response()->json([
                'success' => true,
                'data' => $kategori
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255|unique:KategoriPengajuan,nama',
            'kode' => 'required|string|max:50|unique:KategoriPengajuan,kode',
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'warna' => 'nullable|string|max:7',
            'status' => 'required|in:aktif,nonaktif',
            'settlement' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $kategori = KategoriPengajuan::create([
                'nama' => $request->nama,
                'kode' => strtoupper($request->kode),
                'deskripsi' => $request->deskripsi,
                'icon' => $request->icon ?? 'fas fa-file',
                'warna' => $request->warna ?? '#007bff',
                'status' => $request->status,
                'settlement' => $request->settlement
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan',
                'data' => $kategori
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $kategori = KategoriPengajuan::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $kategori
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255|unique:KategoriPengajuan,nama,' . $id,
            'kode' => 'required|string|max:50|unique:KategoriPengajuan,kode,' . $id,
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'warna' => 'nullable|string|max:7',
            'status' => 'required|in:aktif,nonaktif',
            'settlement' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $kategori = KategoriPengajuan::findOrFail($id);

            $kategori->update([
                'nama' => $request->nama,
                'kode' => strtoupper($request->kode),
                'deskripsi' => $request->deskripsi,
                'icon' => $request->icon ?? 'fas fa-file',
                'warna' => $request->warna ?? '#007bff',
                'status' => $request->status,
                'settlement' => $request->settlement
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diupdate',
                'data' => $kategori
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $kategori = KategoriPengajuan::findOrFail($id);

            // Check if category is being used
            $isUsed = $kategori->pengajuan()->exists() || $kategori->formFields()->exists();

            if ($isUsed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak dapat dihapus karena sedang digunakan'
                ], 422);
            }

            // Delete related flow approvals first
            $kategori->flowApprovals()->delete();
            
            $kategori->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori: ' . $e->getMessage()
            ], 500);
        }
    }
}