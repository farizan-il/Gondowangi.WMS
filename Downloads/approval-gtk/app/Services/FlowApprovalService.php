<?php

namespace App\Services;

use App\Models\FlowApproval;
use App\Models\ProgressApproval;
use App\Models\Pengajuan;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;
use Exception;

class FlowApprovalService
{
    /**
     * Initialize progress approval untuk pengajuan baru
     * Flow sekarang mengikat ke requester spesifik, bukan role/department
     * 
     * @param int $pengajuanId
     * @return bool
     */
    public function initializeProgressApproval($pengajuanId)
    {
        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::with(['kategoriPengajuan', 'requester'])->findOrFail($pengajuanId);
            
            // Ambil flow approval berdasarkan kategori dan requester spesifik
            $flows = FlowApproval::getFlowForRequester(
                $pengajuan->kategori_pengajuan_id, 
                $pengajuan->requester_id
            );

            if ($flows->isEmpty()) {
                throw new Exception('Flow approval tidak ditemukan untuk requester ini pada kategori pengajuan tersebut');
            }

            // Buat progress approval untuk setiap step
            foreach ($flows as $flow) {
                ProgressApproval::create([
                    'pengajuan_id' => $pengajuanId,
                    'flow_approval_id' => $flow->id,
                    'requester_id' => $flow->requester_id,
                    'approver_id' => $flow->approver_id,
                    'step_name' => $flow->nama_step,
                    'urutan' => $flow->urutan,
                    'status' => $flow->urutan == 1 ? 'pending' : 'waiting', // Step pertama langsung pending
                    'step_type' => 'approval'
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Proses approval dari approver spesifik
     * 
     * @param int $progressApprovalId
     * @param int $approverId
     * @param string $action (approved/rejected)
     * @param string $catatan
     * @return array
     */
    public function processApproval($progressApprovalId, $approverId, $action, $catatan = null)
    {
        try {
            DB::beginTransaction();

            $progressApproval = ProgressApproval::with(['pengajuan', 'flowApproval', 'approver'])->findOrFail($progressApprovalId);

            // Validasi apakah approver yang benar
            if ($progressApproval->approver_id != $approverId) {
                throw new Exception('Anda tidak memiliki wewenang untuk approve step ini');
            }

            // Validasi status harus pending
            if ($progressApproval->status != 'pending') {
                throw new Exception('Step ini sudah diproses atau belum saatnya untuk diproses');
            }

            // Update status progress approval
            $progressApproval->update([
                'status' => $action,
                'tanggal_approval' => now(),
                'catatan' => $catatan
            ]);

            $result = [
                'success' => true,
                'message' => 'Approval berhasil diproses',
                'next_step' => null
            ];

            if ($action === 'approved') {
                // Jika approve, aktifkan step selanjutnya
                $nextStep = $this->activateNextStep($progressApproval->pengajuan_id, $progressApproval->urutan);
                
                if ($nextStep) {
                    $result['next_step'] = $nextStep->load(['approver']);
                    $result['message'] = "Approval berhasil, dilanjutkan ke {$nextStep->approver->nama}";
                } else {
                    // Jika tidak ada step selanjutnya, pengajuan selesai
                    $this->completePengajuan($progressApproval->pengajuan_id);
                    $result['message'] = 'Approval berhasil, pengajuan telah selesai diproses';
                }
            } else {
                // Jika reject, batalkan semua step selanjutnya
                $this->rejectPengajuan($progressApproval->pengajuan_id, $progressApproval->urutan);
                $result['message'] = 'Pengajuan telah ditolak';
            }

            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Aktifkan step selanjutnya dalam flow yang sama
     * 
     * @param int $pengajuanId
     * @param int $currentUrutan
     * @return ProgressApproval|null
     */
    private function activateNextStep($pengajuanId, $currentUrutan)
    {
        $nextStep = ProgressApproval::where('pengajuan_id', $pengajuanId)
                                   ->where('urutan', $currentUrutan + 1)
                                   ->where('status', 'waiting')
                                   ->first();

        if ($nextStep) {
            $nextStep->update(['status' => 'pending']);
        }

        return $nextStep;
    }

    /**
     * Selesaikan pengajuan
     * 
     * @param int $pengajuanId
     */
    private function completePengajuan($pengajuanId)
    {
        Pengajuan::where('id', $pengajuanId)->update(['status' => 'approved']);
    }

    /**
     * Tolak pengajuan dan batalkan step selanjutnya
     * 
     * @param int $pengajuanId
     * @param int $rejectedUrutan
     */
    private function rejectPengajuan($pengajuanId, $rejectedUrutan)
    {
        // Update status pengajuan menjadi rejected
        Pengajuan::where('id', $pengajuanId)->update(['status' => 'rejected']);

        // Batalkan semua step yang belum diproses
        ProgressApproval::where('pengajuan_id', $pengajuanId)
                       ->where('urutan', '>', $rejectedUrutan)
                       ->where('status', 'waiting')
                       ->update(['status' => 'cancelled']);
    }

    /**
     * Get progress approval summary dengan detail approver
     * 
     * @param int $pengajuanId
     * @return array
     */
    public function getProgressSummary($pengajuanId)
    {
        $progressApprovals = ProgressApproval::with(['flowApproval', 'approver.department', 'approver.roleLevel'])
                                           ->where('pengajuan_id', $pengajuanId)
                                           ->orderBy('urutan')
                                           ->get();

        $summary = [
            'total_steps' => $progressApprovals->count(),
            'completed_steps' => $progressApprovals->whereIn('status', ['approved', 'rejected'])->count(),
            'current_step' => $progressApprovals->where('status', 'pending')->first(),
            'progress_percentage' => ProgressApproval::getProgressPercentage($pengajuanId),
            'steps' => $progressApprovals->map(function ($progress) {
                return [
                    'id' => $progress->id,
                    'step_name' => $progress->step_name,
                    'urutan' => $progress->urutan,
                    'status' => $progress->status,
                    'approver' => $progress->approver ? [
                        'id' => $progress->approver->id,
                        'nama' => $progress->approver->nama,
                        'email' => $progress->approver->email,
                        'department' => $progress->approver->department->nama ?? null,
                        'role_level' => $progress->approver->roleLevel->nama ?? null
                    ] : null,
                    'tanggal_approval' => $progress->tanggal_approval,
                    'catatan' => $progress->catatan,
                    'can_approve' => $progress->status === 'pending'
                ];
            })
        ];

        return $summary;
    }

    /**
     * Get pending approvals untuk approver spesifik
     * 
     * @param int $approverId
     * @param array $filters
     * @return Collection
     */
    public function getPendingApprovalsForApprover($approverId, $filters = [])
    {
        $query = ProgressApproval::with([
                'pengajuan.requester.department', 
                'pengajuan.kategoriPengajuan',
                'flowApproval'
            ])
            ->pendingForApprover($approverId);

        // Apply filters
        if (isset($filters['kategori_id'])) {
            $query->whereHas('pengajuan', function ($q) use ($filters) {
                $q->where('kategori_pengajuan_id', $filters['kategori_id']);
            });
        }

        if (isset($filters['date_from'])) {
            $query->whereHas('pengajuan', function ($q) use ($filters) {
                $q->whereDate('created_at', '>=', $filters['date_from']);
            });
        }

        if (isset($filters['date_to'])) {
            $query->whereHas('pengajuan', function ($q) use ($filters) {
                $q->whereDate('created_at', '<=', $filters['date_to']);
            });
        }

        if (isset($filters['requester_department'])) {
            $query->whereHas('pengajuan.requester', function ($q) use ($filters) {
                $q->where('department_id', $filters['requester_department']);
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Duplicate flow approval dari satu requester ke requester lain
     * 
     * @param int $sourceKategoriId
     * @param int $sourceRequesterId  
     * @param int $targetKategoriId
     * @param int $targetRequesterId
     * @return bool
     */
    public function duplicateFlow($sourceKategoriId, $sourceRequesterId, $targetKategoriId, $targetRequesterId)
    {
        try {
            DB::beginTransaction();

            // Validasi requester tidak sama (kecuali beda kategori)
            if ($sourceKategoriId == $targetKategoriId && $sourceRequesterId == $targetRequesterId) {
                throw new Exception('Source dan target tidak boleh sama');
            }

            $sourceFlows = FlowApproval::where('kategori_pengajuan_id', $sourceKategoriId)
                                     ->where('requester_id', $sourceRequesterId)
                                     ->where('status', 'active')
                                     ->orderBy('urutan')
                                     ->get();

            if ($sourceFlows->isEmpty()) {
                throw new Exception('Source flow tidak ditemukan');
            }

            // Hapus flow existing di target jika ada
            FlowApproval::where('kategori_pengajuan_id', $targetKategoriId)
                       ->where('requester_id', $targetRequesterId)
                       ->delete();

            // Duplicate flows
            foreach ($sourceFlows as $sourceFlow) {
                FlowApproval::create([
                    'kategori_pengajuan_id' => $targetKategoriId,
                    'requester_id' => $targetRequesterId,
                    'approver_id' => $sourceFlow->approver_id, // Approver tetap sama
                    'urutan' => $sourceFlow->urutan,
                    'nama_step' => $sourceFlow->nama_step,
                    'deskripsi' => $sourceFlow->deskripsi,
                    'status' => 'active'
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Validate flow approval untuk memastikan tidak ada konflik
     * 
     * @param int $kategoriId
     * @param int $requesterId
     * @return array
     */
    public function validateFlowApproval($kategoriId, $requesterId)
    {
        $flows = FlowApproval::getFlowForRequester($kategoriId, $requesterId);
        
        $validation = [
            'is_valid' => true,
            'issues' => [],
            'warnings' => []
        ];

        if ($flows->isEmpty()) {
            $validation['is_valid'] = false;
            $validation['issues'][] = 'Tidak ada flow approval yang dikonfigurasi';
            return $validation;
        }

        // Check untuk duplikat approver dalam satu flow
        $approverCounts = $flows->groupBy('approver_id');
        foreach ($approverCounts as $approverId => $flowsForApprover) {
            if ($flowsForApprover->count() > 1) {
                $approverName = $flowsForApprover->first()->approver->nama;
                $validation['warnings'][] = "Approver {$approverName} muncul lebih dari sekali dalam flow";
            }
        }

        // Check untuk gap dalam urutan
        $urutanArray = $flows->pluck('urutan')->sort()->values()->toArray();
        for ($i = 1; $i <= count($urutanArray); $i++) {
            if (!in_array($i, $urutanArray)) {
                $validation['issues'][] = "Ada gap dalam urutan step (missing step {$i})";
                $validation['is_valid'] = false;
            }
        }

        // Check untuk approver yang tidak aktif
        foreach ($flows as $flow) {
            if ($flow->approver->status !== 'active') {
                $validation['warnings'][] = "Approver {$flow->approver->nama} pada step {$flow->urutan} tidak aktif";
            }
        }

        return $validation;
    }

    /**
     * Get flow statistics untuk dashboard
     * 
     * @param array $filters
     * @return array
     */
    public function getFlowStatistics($filters = [])
    {
        $query = FlowApproval::with(['requester', 'approver', 'kategoriPengajuan']);

        // Apply filters
        if (isset($filters['kategori_id'])) {
            $query->where('kategori_pengajuan_id', $filters['kategori_id']);
        }

        if (isset($filters['department_id'])) {
            $query->whereHas('requester', function($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        $flows = $query->where('status', 'active')->get();

        return [
            'total_flows' => $flows->count(),
            'total_requesters' => $flows->unique('requester_id')->count(),
            'total_approvers' => $flows->unique('approver_id')->count(),
            'avg_steps_per_requester' => $flows->groupBy('requester_id')->map->count()->avg(),
            'categories_with_flows' => $flows->unique('kategori_pengajuan_id')->count(),
            'departments_involved' => $flows->map(function($flow) {
                return $flow->requester->department_id;
            })->unique()->count()
        ];
    }

    /**
     * Auto-assign approvers berdasarkan aturan tertentu
     * Bisa digunakan untuk setup flow otomatis berdasarkan struktur organisasi
     * 
     * @param int $kategoriId
     * @param int $requesterId
     * @param array $rules
     * @return bool
     */
    public function autoAssignApprovers($kategoriId, $requesterId, $rules = [])
    {
        try {
            DB::beginTransaction();

            $requester = Karyawan::with(['department', 'roleLevel'])->findOrFail($requesterId);
            
            // Default rules jika tidak disediakan
            if (empty($rules)) {
                $rules = [
                    ['type' => 'direct_supervisor', 'step_name' => 'Approval Supervisor'],
                    ['type' => 'department_manager', 'step_name' => 'Approval Manager Departemen'],
                    ['type' => 'general_manager', 'step_name' => 'Approval General Manager']
                ];
            }

            // Hapus flow existing
            FlowApproval::where('kategori_pengajuan_id', $kategoriId)
                       ->where('requester_id', $requesterId)
                       ->delete();

            $urutan = 1;
            foreach ($rules as $rule) {
                $approver = $this->findApproverByRule($requester, $rule['type']);
                
                if ($approver) {
                    FlowApproval::create([
                        'kategori_pengajuan_id' => $kategoriId,
                        'requester_id' => $requesterId,
                        'approver_id' => $approver->id,
                        'urutan' => $urutan,
                        'nama_step' => $rule['step_name'],
                        'deskripsi' => 'Auto-generated step',
                        'status' => 'active'
                    ]);
                    
                    $urutan++;
                }
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Helper method untuk mencari approver berdasarkan aturan
     * 
     * @param Karyawan $requester
     * @param string $ruleType
     * @return Karyawan|null
     */
    private function findApproverByRule(Karyawan $requester, $ruleType)
    {
        switch ($ruleType) {
            case 'direct_supervisor':
                // Cari atasan langsung (role level lebih tinggi di department yang sama)
                return Karyawan::where('department_id', $requester->department_id)
                              ->where('role_level_id', '<', $requester->role_level_id)
                              ->where('status', 'active')
                              ->orderBy('role_level_id', 'desc')
                              ->first();
                              
            case 'department_manager':
                // Cari manager/kepala department
                return Karyawan::where('department_id', $requester->department_id)
                              ->whereHas('roleLevel', function($q) {
                                  $q->where('nama', 'like', '%manager%')
                                    ->orWhere('nama', 'like', '%kepala%');
                              })
                              ->where('status', 'active')
                              ->first();
                              
            case 'general_manager':
                // Cari GM atau equivalent
                return Karyawan::whereHas('roleLevel', function($q) {
                                  $q->where('nama', 'like', '%general%')
                                    ->orWhere('nama', 'like', '%gm%')
                                    ->orWhere('level', 1); // Assuming level 1 is highest
                              })
                              ->where('status', 'active')
                              ->first();
                              
            default:
                return null;
        }
    }
}