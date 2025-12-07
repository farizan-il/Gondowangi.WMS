<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlowApproval;
use App\Models\KategoriPengajuan;
use App\Models\Department;
use App\Models\RoleLevel;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KelolaFlowApprovalController extends Controller
{
    public function index()
    {
        $kategoris = KategoriPengajuan::where('status', 'aktif')->get();
        $departments = Department::where('status', 'aktif')->get();
        $karyawans = Karyawan::with(['department', 'roleLevel'])
            ->where('status', 'aktif')
            ->get();
        
        return view('Approval-app.HelpDesk.KelolaFlowApproval.index', compact('kategoris', 'departments', 'karyawans'));
    }

    public function getFlowsByKategori($kategoriId, Request $request)
    {
        $query = FlowApproval::with(['requester.department', 'approver.department'])
            ->where('kategori_pengajuan_id', $kategoriId)
            ->where('status', 'aktif');
        
        // Tambahkan filter department jika ada
        if ($request->has('department_id') && !empty($request->department_id)) {
            $query->whereHas('requester', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        
        $flows = $query->orderBy('requester_id')
            ->orderBy('urutan')
            ->get()
            ->groupBy('requester_id');
        
        return response()->json($flows);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_pengajuan_id' => 'required|exists:KategoriPengajuan,id',
            'requester_id' => 'required|exists:Karyawan,id',
            'flows' => 'required|array|min:1',
            'flows.*.approver_id' => 'required|exists:Karyawan,id|different:requester_id',
            
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Hapus flow lama untuk kategori dan requester ini
            FlowApproval::where('kategori_pengajuan_id', $request->kategori_pengajuan_id)
                       ->where('requester_id', $request->requester_id)
                       ->delete();

            // Buat flow baru
            foreach ($request->flows as $index => $flowData) {
                FlowApproval::create([
                    'kategori_pengajuan_id' => $request->kategori_pengajuan_id,
                    'requester_id' => $request->requester_id,
                    'approver_id' => $flowData['approver_id'],
                    'urutan' => $index + 1,
                    'status' => 'aktif'
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Flow approval berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'flows' => 'required|array|min:1',
            'flows.*.approver_id' => 'required|exists:Karyawan,id',
            'flows.*.nama_step' => 'required|string|max:255',
            'flows.*.deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $flow = FlowApproval::findOrFail($id);

        DB::beginTransaction();
        try {
            // Hapus flow lama untuk kategori dan requester ini
            FlowApproval::where('kategori_pengajuan_id', $flow->kategori_pengajuan_id)
                       ->where('requester_id', $flow->requester_id)
                       ->delete();

            // Buat flow baru
            foreach ($request->flows as $index => $flowData) {
                FlowApproval::create([
                    'kategori_pengajuan_id' => $flow->kategori_pengajuan_id,
                    'requester_id' => $flow->requester_id,
                    'approver_id' => $flowData['approver_id'],
                    'urutan' => $index + 1,
                    'nama_step' => $flowData['nama_step'],
                    'deskripsi' => $flowData['deskripsi'] ?? null,
                    'status' => 'aktif'
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Flow approval berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($kategoriId, $requesterId)
    {
        try {
            FlowApproval::where('kategori_pengajuan_id', $kategoriId)
                       ->where('requester_id', $requesterId)
                       ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Flow approval berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getKaryawanByDepartment($departmentId)
    {
        $karyawans = Karyawan::with(['department', 'roleLevel'])
                           ->where('department_id', $departmentId)
                           ->where('status', 'aktif')
                           ->get();
        
        return response()->json($karyawans);
    }
}