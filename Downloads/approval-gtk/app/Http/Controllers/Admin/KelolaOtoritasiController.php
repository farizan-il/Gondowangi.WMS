<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\KategoriPengajuan;
use App\Models\Department;
use App\Models\RoleLevel;
use App\Models\FlowApproval;

class KelolaOtoritasiController extends Controller
{
    public function create()
    {
        $data = [
            'kategoriPengajuan' => KategoriPengajuan::where('status', 'aktif')->orderBy('nama')->get(),
            'departments' => Department::where('status', 'aktif')->orderBy('nama')->get(),
            'roleLevels' => RoleLevel::orderBy('id')->get()
        ];

        return view('Approval-app.HelpDesk.KelolaOtoritasi.create', $data);
    }

    /**
     * Store workflow configuration
     */
    // public function store(Request $request)
    // {
    //     // Validation rules
    //     $validator = Validator::make($request->all(), [
    //         'kategori_pengajuan_id' => 'required|exists:KategoriPengajuan,id',
    //         'department_id' => 'required|exists:Department,id',
    //         'steps' => 'required|array|min:1',
    //         'steps.*.nama_step' => 'required|string|max:100',
    //         'steps.*.role_level_id' => 'required|exists:RoleLevel,id',
    //         'steps.*.deskripsi' => 'nullable|string|max:500'
    //     ], [
    //         'kategori_pengajuan_id.required' => 'Kategori pengajuan harus dipilih',
    //         'kategori_pengajuan_id.exists' => 'Kategori pengajuan tidak valid',
    //         'department_id.required' => 'Departemen harus dipilih',
    //         'department_id.exists' => 'Departemen tidak valid',
    //         'steps.required' => 'Minimal harus ada satu step approval',
    //         'steps.min' => 'Minimal harus ada satu step approval',
    //         'steps.*.nama_step.required' => 'Nama step harus diisi',
    //         'steps.*.nama_step.max' => 'Nama step maksimal 100 karakter',
    //         'steps.*.role_level_id.required' => 'Role level harus dipilih',
    //         'steps.*.role_level_id.exists' => 'Role level tidak valid',
    //         'steps.*.deskripsi.max' => 'Deskripsi maksimal 500 karakter'
    //     ]);

    //     if ($validator->fails()) {
    //         return redirect()->back()
    //             ->withErrors($validator)
    //             ->withInput();
    //     }

    //     try {
    //         DB::beginTransaction();

    //         $kategoriId = $request->kategori_pengajuan_id;
    //         $departmentId = $request->department_id;
    //         $steps = $request->steps;

    //         // Check if workflow already exists for this category and department
    //         $existingFlow = FlowApproval::where('kategori_pengajuan_id', $kategoriId)
    //             ->where('department_id', $departmentId)
    //             ->first();

    //         if ($existingFlow) {
    //             // Delete existing workflow steps
    //             FlowApproval::where('kategori_pengajuan_id', $kategoriId)
    //                 ->where('department_id', $departmentId)
    //                 ->delete();
    //         }

    //         // Validate role level sequence (optional - ensure proper hierarchy)
    //         $this->validateRoleLevelSequence($steps);

    //         // Create new workflow steps
    //         foreach ($steps as $index => $step) {
    //             FlowApproval::create([
    //                 'kategori_pengajuan_id' => $kategoriId,
    //                 'department_id' => $departmentId,
    //                 'urutan' => $index + 1,
    //                 'role_level_id' => $step['role_level_id'],
    //                 'nama_step' => trim($step['nama_step']),
    //                 'deskripsi' => !empty($step['deskripsi']) ? trim($step['deskripsi']) : null,
    //                 'status' => 'aktif'
    //             ]);
    //         }

    //         DB::commit();

    //         // Get category and department names for success message
    //         $kategori = KategoriPengajuan::find($kategoriId);
    //         $department = Department::find($departmentId);

    //         return redirect()->back()->with('success', 
    //             "Flow approval untuk kategori \"{$kategori->nama}\" departemen \"{$department->nama}\" berhasil disimpan dengan " . count($steps) . " step.");

    //     } catch (\Exception $e) {
    //         DB::rollback();
            
    //         // Log the error
    //         \Log::error('Error saving workflow configuration: ' . $e->getMessage(), [
    //             'kategori_pengajuan_id' => $request->kategori_pengajuan_id,
    //             'department_id' => $request->department_id,
    //             'steps' => $request->steps,
    //             'user_id' => auth()->id(),
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         return redirect()->back()
    //             ->with('error', 'Terjadi kesalahan saat menyimpan konfigurasi flow approval. Silakan coba lagi.')
    //             ->withInput();
    //     }
    // }
    
    // === LOGIC TESTER UNTUK CONTROLLER ====
    public function store(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'kategori_pengajuan_id' => 'required|exists:KategoriPengajuan,id',
            'department_id' => 'required|exists:Department,id',
            'steps' => 'required|array|min:1',
            'steps.*.nama_step' => 'required|string|max:100',
            'steps.*.role_level_id' => 'required|exists:RoleLevel,id',
            'steps.*.deskripsi' => 'nullable|string|max:500'
        ], [
            'kategori_pengajuan_id.required' => 'Kategori pengajuan harus dipilih',
            'kategori_pengajuan_id.exists' => 'Kategori pengajuan tidak valid',
            'department_id.required' => 'Departemen harus dipilih',
            'department_id.exists' => 'Departemen tidak valid',
            'steps.required' => 'Minimal harus ada satu step approval',
            'steps.min' => 'Minimal harus ada satu step approval',
            'steps.*.nama_step.required' => 'Nama step harus diisi',
            'steps.*.nama_step.max' => 'Nama step maksimal 100 karakter',
            'steps.*.role_level_id.required' => 'Role level harus dipilih',
            'steps.*.role_level_id.exists' => 'Role level tidak valid',
            'steps.*.deskripsi.max' => 'Deskripsi maksimal 500 karakter'
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        try {
            DB::beginTransaction();
    
            $kategoriId = $request->kategori_pengajuan_id;
            $departmentId = $request->department_id;
            $steps = $request->steps;
    
            // Check if workflow already exists for this category and department
            $existingFlow = FlowApproval::where('kategori_pengajuan_id', $kategoriId)
                ->where('department_id', $departmentId)
                ->first();
    
            if ($existingFlow) {
                // Delete existing workflow steps
                FlowApproval::where('kategori_pengajuan_id', $kategoriId)
                    ->where('department_id', $departmentId)
                    ->delete();
            }
    
            // Validate role level sequence (optional - ensure proper hierarchy)
            $this->validateRoleLevelSequence($steps);
    
            // Create new workflow steps
            foreach ($steps as $index => $step) {
                FlowApproval::create([
                    'kategori_pengajuan_id' => $kategoriId,
                    'department_id' => $departmentId,
                    'urutan' => $index + 1,
                    'role_level_id' => $step['role_level_id'],
                    'nama_step' => trim($step['nama_step']),
                    'deskripsi' => !empty($step['deskripsi']) ? trim($step['deskripsi']) : null,
                    'status' => 'aktif'
                ]);
            }
    
            DB::commit();
    
            // Get category and department names for success message
            $kategori = KategoriPengajuan::find($kategoriId);
            $department = Department::find($departmentId);
    
            return redirect()->back()->with('success', 
                "Flow approval untuk kategori \"{$kategori->nama}\" departemen \"{$department->nama}\" berhasil disimpan dengan " . count($steps) . " step.");
    
        } catch (\Exception $e) {
            DB::rollback();
            
            // Log the detailed error
            \Log::error('Error saving workflow configuration', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'kategori_pengajuan_id' => $request->kategori_pengajuan_id,
                'department_id' => $request->department_id,
                'steps' => $request->steps,
                'user_id' => auth()->id(),
                'stack_trace' => $e->getTraceAsString()
            ]);
    
            // Determine error message based on environment
            $errorMessage = 'Terjadi kesalahan saat menyimpan konfigurasi flow approval.';
            
            // Show detailed error in development environment
            if (config('app.debug')) {
                $errorMessage .= ' Detail Error: ' . $e->getMessage();
                
                // Add more context for common database errors
                if (strpos($e->getMessage(), 'SQLSTATE') !== false) {
                    $errorMessage .= ' (Database Error)';
                } elseif (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                    $errorMessage .= ' (Foreign Key Constraint Error)';
                } elseif (strpos($e->getMessage(), 'duplicate entry') !== false) {
                    $errorMessage .= ' (Duplicate Entry Error)';
                }
                
                $errorMessage .= ' di file: ' . basename($e->getFile()) . ' baris: ' . $e->getLine();
            } else {
                $errorMessage .= ' Silakan coba lagi atau hubungi administrator jika masalah berlanjut.';
            }
    
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        } catch (\Throwable $e) {
            // Catch any other throwable errors (PHP 7+)
            DB::rollback();
            
            \Log::critical('Critical error in workflow configuration', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_class' => get_class($e),
                'kategori_pengajuan_id' => $request->kategori_pengajuan_id,
                'department_id' => $request->department_id,
                'user_id' => auth()->id(),
                'stack_trace' => $e->getTraceAsString()
            ]);
    
            $errorMessage = 'Terjadi kesalahan sistem yang tidak terduga.';
            
            if (config('app.debug')) {
                $errorMessage .= ' Detail: ' . $e->getMessage() . ' di ' . basename($e->getFile()) . ':' . $e->getLine();
            } else {
                $errorMessage .= ' Silakan hubungi administrator sistem.';
            }
    
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    /**
     * Get existing flow for specific category and department
     */
    public function getExistingFlow(Request $request)
    {
        try {
            $kategoriId = $request->get('kategori_id');
            $departmentId = $request->get('department_id');

            if (!$kategoriId || !$departmentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter tidak lengkap'
                ]);
            }

            $flows = FlowApproval::with('roleLevel')
                ->where('kategori_pengajuan_id', $kategoriId)
                ->where('department_id', $departmentId)
                ->orderBy('urutan')
                ->get();

            return response()->json([
                'success' => true,
                'flows' => $flows
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data flow'
            ]);
        }
    }

    /**
     * Display list of existing workflow configurations
     */
    public function index()
    {
        $workflows = FlowApproval::with(['kategoriPengajuan', 'department', 'roleLevel'])
            ->orderBy('kategori_pengajuan_id')
            ->orderBy('department_id')
            ->orderBy('urutan')
            ->get()
            ->groupBy(function($item) {
                return $item->kategori_pengajuan_id . '_' . $item->department_id;
            });

        return view('Approval-app.HelpDesk.KelolaOtoritasi.index', compact('workflows'));
    }

    /**
     * Edit specific workflow configuration
     */
    public function edit($kategoriId, $departmentId)
    {
        $flows = FlowApproval::with(['kategoriPengajuan', 'department', 'roleLevel'])
            ->where('kategori_pengajuan_id', $kategoriId)
            ->where('department_id', $departmentId)
            ->orderBy('urutan')
            ->get();

        if ($flows->isEmpty()) {
            return redirect()->route('admin.kelola-otoritasi.index')
                ->with('error', 'Flow approval tidak ditemukan');
        }

        $data = [
            'flows' => $flows,
            'kategoriPengajuan' => KategoriPengajuan::where('status', 'aktif')->orderBy('nama')->get(),
            'departments' => Department::where('status', 'aktif')->orderBy('nama')->get(),
            'roleLevels' => RoleLevel::orderBy('level')->get(),
            'selectedKategori' => $kategoriId,
            'selectedDepartment' => $departmentId
        ];

        return view('Approval-app.HelpDesk.KelolaOtoritasi.edit', $data);
    }

    /**
     * Update workflow configuration
     */
    public function update(Request $request, $kategoriId, $departmentId)
    {
        // Use the same validation and logic as store method
        $request->merge([
            'kategori_pengajuan_id' => $kategoriId,
            'department_id' => $departmentId
        ]);

        return $this->store($request);
    }

    /**
     * Delete workflow configuration
     */
    public function destroy($kategoriId, $departmentId)
    {
        try {
            DB::beginTransaction();

            // Check if there are any pending approvals using this workflow
            $hasActivePengajuan = DB::table('pengajuan')
                ->join('progress_approval', 'pengajuan.id', '=', 'progress_approval.pengajuan_id')
                ->join('flow_approval', 'progress_approval.flow_approval_id', '=', 'flow_approval.id')
                ->where('flow_approval.kategori_pengajuan_id', $kategoriId)
                ->where('flow_approval.department_id', $departmentId)
                ->where('pengajuan.status_pengajuan', 'in_progress')
                ->exists();

            if ($hasActivePengajuan) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus flow approval yang sedang digunakan untuk pengajuan aktif');
            }

            // Delete the workflow
            $deleted = FlowApproval::where('kategori_pengajuan_id', $kategoriId)
                ->where('department_id', $departmentId)
                ->delete();

            if ($deleted) {
                DB::commit();
                return redirect()->route('admin.kelola-otoritasi.index')
                    ->with('success', 'Flow approval berhasil dihapus');
            } else {
                return redirect()->back()
                    ->with('error', 'Flow approval tidak ditemukan');
            }

        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Error deleting workflow configuration: ' . $e->getMessage(), [
                'kategori_pengajuan_id' => $kategoriId,
                'department_id' => $departmentId,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus flow approval');
        }
    }

    /**
     * Toggle workflow status (aktif/nonaktif)
     */
    public function toggleStatus($kategoriId, $departmentId)
    {
        try {
            $flows = FlowApproval::where('kategori_pengajuan_id', $kategoriId)
                ->where('department_id', $departmentId)
                ->get();

            if ($flows->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'Flow approval tidak ditemukan');
            }

            $newStatus = $flows->first()->status === 'aktif' ? 'nonaktif' : 'aktif';
            
            FlowApproval::where('kategori_pengajuan_id', $kategoriId)
                ->where('department_id', $departmentId)
                ->update(['status' => $newStatus]);

            $statusText = $newStatus === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';
            
            return redirect()->back()
                ->with('success', "Flow approval berhasil {$statusText}");

        } catch (\Exception $e) {
            \Log::error('Error toggling workflow status: ' . $e->getMessage(), [
                'kategori_pengajuan_id' => $kategoriId,
                'department_id' => $departmentId,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengubah status flow approval');
        }
    }

    /**
     * Validate role level sequence to ensure proper hierarchy
     */
    private function validateRoleLevelSequence($steps)
    {
        $roleLevels = RoleLevel::whereIn('id', array_column($steps, 'role_level_id'))
            ->pluck('id')
            ->toArray();

        $previousLevel = 0;
        foreach ($steps as $index => $step) {
            $currentLevel = $roleLevels[$step['role_level_id']] ?? 0;
            
            // Optional: Uncomment this if you want to enforce ascending role levels
            // if ($currentLevel <= $previousLevel) {
            //     throw new \Exception("Role level pada step " . ($index + 1) . " harus lebih tinggi dari step sebelumnya");
            // }
            
            $previousLevel = $currentLevel;
        }
    }

    /**
     * Get workflow statistics
     */
    public function getWorkflowStats()
    {
        try {
            $stats = [
                'total_workflows' => DB::table('flow_approval')
                    ->select('kategori_pengajuan_id', 'department_id')
                    ->distinct()
                    ->count(),
                'active_workflows' => DB::table('flow_approval')
                    ->select('kategori_pengajuan_id', 'department_id')
                    ->where('status', 'aktif')
                    ->distinct()
                    ->count(),
                'total_steps' => FlowApproval::count(),
                'categories_with_workflow' => DB::table('flow_approval')
                    ->select('kategori_pengajuan_id')
                    ->distinct()
                    ->count(),
                'departments_with_workflow' => DB::table('flow_approval')
                    ->select('department_id')
                    ->distinct()
                    ->count()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting workflow statistics'
            ]);
        }
    }

    /**
     * Duplicate workflow from one department to another
     */
    public function duplicate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'source_kategori_id' => 'required|exists:KategoriPengajuan,id',
            'source_department_id' => 'required|exists:department,id',
            'target_department_ids' => 'required|array|min:1',
            'target_department_ids.*' => 'exists:department,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $sourceFlows = FlowApproval::where('kategori_pengajuan_id', $request->source_kategori_id)
                ->where('department_id', $request->source_department_id)
                ->orderBy('urutan')
                ->get();

            if ($sourceFlows->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'Source workflow tidak ditemukan');
            }

            $duplicatedCount = 0;
            foreach ($request->target_department_ids as $targetDepartmentId) {
                // Skip if trying to duplicate to same department
                if ($targetDepartmentId == $request->source_department_id) {
                    continue;
                }

                // Delete existing workflow for target department if exists
                FlowApproval::where('kategori_pengajuan_id', $request->source_kategori_id)
                    ->where('department_id', $targetDepartmentId)
                    ->delete();

                // Duplicate flows
                foreach ($sourceFlows as $flow) {
                    FlowApproval::create([
                        'kategori_pengajuan_id' => $flow->kategori_pengajuan_id,
                        'department_id' => $targetDepartmentId,
                        'urutan' => $flow->urutan,
                        'role_level_id' => $flow->role_level_id,
                        'nama_step' => $flow->nama_step,
                        'deskripsi' => $flow->deskripsi,
                        'status' => $flow->status
                    ]);
                }
                $duplicatedCount++;
            }

            DB::commit();

            return redirect()->back()
                ->with('success', "Workflow berhasil diduplikasi ke {$duplicatedCount} departemen");

        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Error duplicating workflow: ' . $e->getMessage(), [
                'source_kategori_id' => $request->source_kategori_id,
                'source_department_id' => $request->source_department_id,
                'target_department_ids' => $request->target_department_ids,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menduplikasi workflow');
        }
    }
}