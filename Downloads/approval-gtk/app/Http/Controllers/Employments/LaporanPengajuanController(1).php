<?php

namespace App\Http\Controllers\Employments;

use App\Http\Controllers\Controller;
use App\Models\KategoriPengajuan;
use App\Models\FormField;
use App\Models\Pengajuan;
use App\Models\DetailPengajuan;
use App\Models\FlowApproval;
use App\Models\ProgressApproval;
use App\Models\Settlement;           // TAMBAHKAN INI
use App\Models\DetailSettlement;     // TAMBAHKAN INI
use App\Models\Karyawan;
use App\Models\EmailNotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\EmailNotificationService;
use App\Models\HistoryPengajuan;

class LaporanPengajuanController extends Controller
{
    protected $emailService;

    public function __construct(EmailNotificationService $emailService)
    {
        $this->emailService = $emailService;
    }
    
    public function index()
    {
        $userId = Auth::id();
        
        try {
            // Ambil data pengajuan yang perlu diapprove oleh user yang login
            $pengajuanList = Pengajuan::with([
                'kategoriPengajuan',
                'requester.department',
                'detailPengajuan.formField',
                'progressApprovals' => function($query) use ($userId) {
                    $query->where('approver_id', $userId);
                }
            ])
            ->whereHas('progressApprovals', function($query) use ($userId) {
                $query->where('approver_id', $userId)
                      ->whereIn('status', ['proses', 'pending']);
            })
            ->orderBy('created_at', 'desc')
            ->get();

            return view('Approval-app.Karyawan.LaporanPengajuan.index', compact('pengajuanList'));
        } catch (\Exception $e) {
            Log::error('Error in LaporanPengajuanController@index: ' . $e->getMessage());
            return back()->withErrors('Terjadi kesalahan saat memuat data pengajuan.');
        }
    }

    /**
     * Method detail - Perbaikan utama dengan error handling yang lebih baik
     */
    public function detail($id)
    {
        try {
            $userId = Auth::id();
            
            Log::info("Loading detail for pengajuan ID: {$id}, User ID: {$userId}");
            
            // Validasi ID
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID pengajuan tidak valid'
                ], 400);
            }
            
            // Ambil detail pengajuan dengan relasi lengkap
            $pengajuan = Pengajuan::with([
                'kategoriPengajuan',
                'requester', // Perbaikan: hapus .department karena bisa null
                'requester.department', // Load relasi secara terpisah
                'detailPengajuan',
                'detailPengajuan.formField',
                'progressApprovals' => function($query) {
                    $query->orderBy('urutan');
                },
                'progressApprovals.approver',
                'progressApprovals.approver.department',
                'historyPengajuan'
            ])
            ->whereHas('progressApprovals', function($query) use ($userId) {
                $query->where('approver_id', $userId);
            })
            ->find($id);
    
            // Cek apakah pengajuan ditemukan
            if (!$pengajuan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan tidak ditemukan atau Anda tidak memiliki akses'
                ], 404);
            }
            
            $isSettlementRequest = $pengajuan->progressApprovals()
                ->whereNotNull('settlement_id')
                ->exists();
            
            $settlementData = null;
            if ($isSettlementRequest) {
                // Ambil settlement_id dari progressApproval
                $settlementId = $pengajuan->progressApprovals()
                    ->whereNotNull('settlement_id')
                    ->first()
                    ->settlement_id;
                
                // Load data settlement dengan relasi lengkap
                $settlement = \App\Models\Settlement::with([
                    'details' => function($query) {
                        $query->orderBy('created_at');
                    },
                    'pengajuan' => function($query) {
                        $query->with(['requester', 'kategoriPengajuan']);
                    }
                ])->find($settlementId);
                
                if ($settlement) {
                    $settlementData = [
                        'id' => $settlement->id,
                        'nomor_settlement' => $settlement->nomor_settlement,
                        'tanggal_settlement' => $settlement->tanggal_settlement,
                        'total_actual' => $settlement->total_actual,
                        'selisih' => $settlement->selisih,
                        'status_settlement' => $settlement->status_settlement,
                        'catatan_settlement' => $settlement->catatan_settlement,
                        'pengajuan_original' => [
                            'nomor_pengajuan' => $settlement->pengajuan->nomor_pengajuan,
                            'judul' => $settlement->pengajuan->judul,
                            'nominal_pengajuan' => $settlement->pengajuan->nominal_pengajuan,
                            'mata_uang' => $settlement->pengajuan->mata_uang,
                        ],
                        'details' => $settlement->details->map(function($detail) {
                            return [
                                'id' => $detail->id,
                                'keterangan' => $detail->keterangan,
                                'tanggal_transaksi' => $detail->tanggal_transaksi,
                                'nominal' => $detail->nominal,
                                'kategori_biaya' => $detail->kategori_biaya,
                                'file_bukti' => $detail->file_bukti,
                                'catatan' => $detail->catatan
                            ];
                        })
                    ];
                }
            }
            
            // MODIFIKASI: Update progressData untuk menambahkan settlement_id
            foreach ($pengajuan->progressApprovals as $index => $progress) {
                if (isset($progressData[$index])) {
                    $progressData[$index]['settlement_id'] = $progress->settlement_id; // Tambahkan ini
                }
            }
    
            // Transformasi data detail pengajuan untuk kemudahan akses di frontend
            $detailFields = [];
            if ($pengajuan->detailPengajuan) {
                foreach ($pengajuan->detailPengajuan as $detail) {
                    if ($detail && $detail->formField) { // Pastikan relasi ada
                        $detailFields[] = [
                            'name' => $detail->formField->nama_field ?? 'unknown_field',
                            'label' => $detail->formField->label ?? 'Unknown Field',
                            'type' => $detail->formField->tipe_field ?? 'text',
                            'value' => $detail->nilai ?? '',
                            'urutan' => $detail->formField->urutan ?? 999
                        ];
                    }
                }
            }
    
            // Urutkan berdasarkan urutan field
            usort($detailFields, function($a, $b) {
                return $a['urutan'] <=> $b['urutan'];
            });
    
            // Transformasi data progress approval untuk timeline
            $progressData = [];
            $currentUserProgress = null;
            
            if ($pengajuan->progressApprovals) {
                foreach ($pengajuan->progressApprovals as $progress) {
                    $progressItem = [
                        'urutan' => $progress->urutan ?? 1,
                        'step_name' => $progress->step_name ?? "Step {$progress->urutan}",
                        'approver_name' => ($progress->approver) ? $progress->approver->nama : 'Belum ditentukan',
                        'approver_email' => ($progress->approver) ? $progress->approver->email : null,
                        'department' => ($progress->approver && $progress->approver->department) ? $progress->approver->department->nama : '-',
                        'status' => $progress->status ?? 'pending',
                        'tanggal_approval' => $progress->tanggal_approval,
                        'catatan' => $progress->catatan,
                        'is_current' => ($progress->urutan == $pengajuan->current_step),
                        'is_completed' => in_array($progress->status, ['approved', 'completed']),
                        'is_rejected' => ($progress->status == 'rejected')
                    ];
                    
                    // Cek apakah ini progress milik user yang login
                    if ($progress->approver_id == $userId) {
                        $currentUserProgress = $progressItem;
                    }
                    
                    $progressData[] = $progressItem;
                }
            }
    
            // Format data untuk response dengan null check yang lebih baik
            $responseData = [
                'id' => $pengajuan->id,
                'nomor_pengajuan' => $pengajuan->nomor_pengajuan ?? '-',
                'judul' => $pengajuan->judul ?? '-',
                'deskripsi' => $pengajuan->deskripsi ?? null, // Biarkan null untuk pengecekan di view
                'kategori_pengajuan_id' => $pengajuan->kategori_pengajuan_id,
                'kategori_pengajuan' => [
                    'nama' => ($pengajuan->kategoriPengajuan) ? $pengajuan->kategoriPengajuan->nama : '-'
                ],
                'nominal_pengajuan' => $pengajuan->nominal_pengajuan ?? 0,
                'mata_uang' => $pengajuan->mata_uang ?? 'IDR',
                'tanggal_pengajuan' => $pengajuan->tanggal_pengajuan,
                'tanggal_kebutuhan' => $pengajuan->tanggal_kebutuhan,
                'status_pengajuan' => $pengajuan->status_pengajuan ?? 'pending',
                'current_step' => $pengajuan->current_step ?? 1,
                'total_step' => $pengajuan->total_step ?? 1,
                'catatan_requester' => $pengajuan->catatan_requester,
                'file_pendukung' => $pengajuan->file_pendukung ?? [],
                'requester' => [
                    'nama' => ($pengajuan->requester) ? $pengajuan->requester->nama : '-',
                    'email' => ($pengajuan->requester) ? $pengajuan->requester->email : '-',
                    'department' => ($pengajuan->requester && $pengajuan->requester->department) ? $pengajuan->requester->department->nama : '-'
                ],
                'detail_fields' => $detailFields,
                'progress_data' => $progressData,
                'current_user_progress' => $currentUserProgress,
                'can_approve' => $currentUserProgress && 
                               in_array($currentUserProgress['status'], ['pending', 'proses']) && 
                               $currentUserProgress['is_current']
            ];
    
            Log::info("Detail loaded successfully for pengajuan ID: {$id}");
    
            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);
    
        } catch (\Exception $e) {
            Log::error("Error in detail method: " . $e->getMessage() . " | File: " . $e->getFile() . " | Line: " . $e->getLine() . " | Trace: " . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail pengajuan. Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
    public function settlementDetail($pengajuanId)
    {
        try {
            $userId = Auth::id();
            
            Log::info("Loading settlement detail for pengajuan ID: {$pengajuanId}, User ID: {$userId}");
            
            // Validasi ID
            if (!is_numeric($pengajuanId) || $pengajuanId <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID pengajuan tidak valid'
                ], 400);
            }
            
            // Ambil pengajuan dengan relasi settlement
            $pengajuan = Pengajuan::with([
                'kategoriPengajuan',
                'requester.department',
                'progressApprovals' => function($query) {
                    $query->orderBy('urutan')
                          ->with(['approver.department', 'settlement']);
                }
            ])
            ->whereHas('progressApprovals', function($query) use ($userId) {
                $query->where('approver_id', $userId)
                      ->whereNotNull('settlement_id'); // Hanya yang punya settlement
            })
            ->find($pengajuanId);
    
            if (!$pengajuan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan settlement tidak ditemukan atau Anda tidak memiliki akses'
                ], 404);
            }
    
            // Ambil settlement data melalui progress approval
            $settlementProgress = $pengajuan->progressApprovals()
                ->whereNotNull('settlement_id')
                ->first();
                
            if (!$settlementProgress || !$settlementProgress->settlement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data settlement tidak ditemukan'
                ], 404);
            }
    
            $settlement = $settlementProgress->settlement;
            
            // Load settlement dengan relasi
            $settlement->load(['details', 'pengajuan']);
    
            // PERBAIKAN: Ambil progress approval settlement dari table ProgressApproval berdasarkan settlement_id
            $settlementProgressApprovals = \App\Models\ProgressApproval::where('settlement_id', $settlement->id)
                ->with(['approver.department'])
                ->orderBy('urutan')
                ->get();
    
            Log::info("Found settlement progress approvals: " . $settlementProgressApprovals->count() . " for settlement ID: " . $settlement->id);
    
            // Debug logging untuk memastikan data progress
            foreach ($settlementProgressApprovals as $debugProgress) {
                Log::info("Settlement Progress Debug - ID: {$debugProgress->id}, Settlement ID: {$debugProgress->settlement_id}, Urutan: {$debugProgress->urutan}, Status: {$debugProgress->status}, Approver: " . ($debugProgress->approver ? $debugProgress->approver->nama : 'null'));
            }
    
            // Transform progress data untuk timeline settlement
            $progressData = [];
            $currentUserProgress = null;
            
            if ($settlementProgressApprovals->isNotEmpty()) {
                foreach ($settlementProgressApprovals as $progress) {
                    $progressItem = [
                        'urutan' => $progress->urutan ?? 1,
                        'step_name' => $progress->step_name ?? "Settlement Step {$progress->urutan}",
                        'approver_name' => ($progress->approver) ? $progress->approver->nama : 'Belum ditentukan',
                        'approver_email' => ($progress->approver) ? $progress->approver->email : null,
                        'department' => ($progress->approver && $progress->approver->department) ? $progress->approver->department->nama : '-',
                        'status' => $progress->status ?? 'pending',
                        'tanggal_approval' => $progress->tanggal_approval,
                        'catatan' => $progress->catatan,
                        'is_current' => ($progress->urutan == $settlement->current_step),
                        'is_completed' => in_array($progress->status, ['approved', 'completed']),
                        'is_rejected' => ($progress->status == 'rejected'),
                        'settlement_id' => $progress->settlement_id
                    ];
                    
                    if ($progress->approver_id == $userId) {
                        $currentUserProgress = $progressItem;
                    }
                    
                    $progressData[] = $progressItem;
                }
            } else {
                Log::warning("No settlement progress approvals found for settlement ID: " . $settlement->id);
            }
    
            // Format response data
            $responseData = [
                'pengajuan' => [
                    'id' => $pengajuan->id,
                    'nomor_pengajuan' => $pengajuan->nomor_pengajuan ?? '-',
                    'judul' => $pengajuan->judul ?? '-',
                    'nominal_pengajuan' => $pengajuan->nominal_pengajuan ?? 0,
                    'mata_uang' => $pengajuan->mata_uang ?? 'IDR',
                    'requester' => [
                        'nama' => ($pengajuan->requester) ? $pengajuan->requester->nama : '-',
                        'email' => ($pengajuan->requester) ? $pengajuan->requester->email : '-',
                        'department' => ($pengajuan->requester && $pengajuan->requester->department) ? $pengajuan->requester->department->nama : '-'
                    ]
                ],
                'settlement' => [
                    'id' => $settlement->id,
                    'nomor_settlement' => $settlement->nomor_settlement ?? '-',
                    'tanggal_settlement' => $settlement->tanggal_settlement,
                    'total_actual' => $settlement->total_actual ?? 0,
                    'selisih' => $settlement->selisih ?? 0,
                    'status_settlement' => $settlement->status_settlement ?? 'pending',
                    'catatan_settlement' => $settlement->catatan_settlement,
                    'file_bukti_transfer' => $settlement->file_bukti_transfer,
                    'tanggal_transfer' => $settlement->tanggal_transfer,
                    'catatan_transfer' => $settlement->catatan_transfer,
                    'current_step' => $settlement->current_step ?? 1,
                    'total_step' => $settlement->total_step ?? 1
                ],
                'details' => $settlement->details->map(function($detail) {
                    return [
                        'id' => $detail->id,
                        'keterangan' => $detail->keterangan ?? '-',
                        'tanggal_transaksi' => $detail->tanggal_transaksi,
                        'nominal' => $detail->nominal ?? 0,
                        'kategori_biaya' => $detail->kategori_biaya,
                        'file_bukti' => $detail->file_bukti,
                        'catatan' => $detail->catatan
                    ];
                }),
                'progress_data' => $progressData,
                'current_user_progress' => $currentUserProgress,
                'can_approve' => $currentUserProgress && 
                               in_array($currentUserProgress['status'], ['pending', 'proses']) && 
                               $currentUserProgress['is_current']
            ];
    
            Log::info("Settlement detail loaded successfully for pengajuan ID: {$pengajuanId}");
    
            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);
    
        } catch (\Exception $e) {
            Log::error("Error in settlementDetail method: " . $e->getMessage() . " | File: " . $e->getFile() . " | Line: " . $e->getLine());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail settlement. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update status pengajuan
     */
    public function updateSettlementStatus(Request $request, $pengajuanId)
    {
        DB::beginTransaction(); // Start database transaction
        
        try {
            $userId = Auth::id();
            
            // Validasi input
            $request->validate([
                'status' => 'required|in:approved,rejected,revision',
                'catatan' => 'nullable|string|max:1000'
            ]);
    
            Log::info("Settlement approval attempt - User ID: {$userId}, Pengajuan ID: {$pengajuanId}, Status: {$request->status}");
    
            // Cari pengajuan dengan relasi
            $pengajuan = Pengajuan::with(['requester', 'kategoriPengajuan'])->find($pengajuanId);
            if (!$pengajuan) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan tidak ditemukan'
                ], 404);
            }
    
            // Ambil settlement melalui progress approval yang memiliki settlement_id
            $settlementProgressApproval = ProgressApproval::where('pengajuan_id', $pengajuanId)
                ->where('approver_id', $userId)
                ->whereNotNull('settlement_id')
                ->first();
    
            if (!$settlementProgressApproval) {
                DB::rollBack();
                Log::warning("Settlement progress approval not found for user {$userId} and pengajuan {$pengajuanId}");
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menyetujui settlement ini'
                ], 403);
            }
    
            $settlement = Settlement::find($settlementProgressApproval->settlement_id);
            if (!$settlement) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Data settlement tidak ditemukan'
                ], 404);
            }
    
            // Cari progress approval settlement yang sesuai dengan user dan current step
            $currentSettlementProgress = ProgressApproval::where('settlement_id', $settlement->id)
                ->where('approver_id', $userId)
                ->where('urutan', $settlement->current_step)
                ->first();
    
            if (!$currentSettlementProgress) {
                DB::rollBack();
                Log::warning("Current settlement progress not found - Settlement ID: {$settlement->id}, User: {$userId}, Current Step: {$settlement->current_step}");
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menyetujui settlement pada step ini'
                ], 403);
            }
    
            // Validasi status saat ini
            if (!in_array($currentSettlementProgress->status, ['pending', 'proses'])) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Settlement sudah diproses sebelumnya'
                ], 409);
            }
    
            // Simpan status sebelumnya untuk history
            $statusBefore = $settlement->status_settlement;
            $currentUser = Auth::user();
    
            // Update progress approval settlement
            $currentSettlementProgress->update([
                'status' => $request->status,
                'catatan' => $request->catatan,
                'tanggal_approval' => now()
            ]);
    
            Log::info("Settlement progress updated - ID: {$currentSettlementProgress->id}, Status: {$request->status}");
    
            // Flag untuk menentukan apakah ini layer terakhir dan email berhasil dikirim
            $isLastLayer = false;
            $emailSent = false;
    
            // Update settlement berdasarkan status
            if ($request->status === 'approved') {
                // Jika approved dan masih ada step selanjutnya
                if ($settlement->current_step < $settlement->total_step) {
                    $settlement->current_step += 1;
                    $settlement->status_settlement = 'proses'; // Status tetap proses jika masih ada step selanjutnya
                    Log::info("Settlement moved to next step - Settlement ID: {$settlement->id}, New Step: {$settlement->current_step}");
                } else {
                    // Jika sudah step terakhir (LAYER TERAKHIR)
                    $isLastLayer = true;
                    $settlement->status_settlement = 'approved';
                    
                    Log::info("Settlement fully approved - Settlement ID: {$settlement->id}");
                    
                    // Update pengajuan asli menjadi completed
                    $pengajuan->status_pengajuan = 'completed';
                    $pengajuan->save();
                    
                    Log::info("Pengajuan status updated to completed - Pengajuan ID: {$pengajuan->id}");
                    
                    // KIRIM EMAIL NOTIFIKASI KE REQUESTER (HANYA DI LAYER TERAKHIR)
                    try {
                        $emailSent = $this->sendSettlementApprovedNotification($pengajuan, $settlement, $currentUser);
                        Log::info("Email notification attempt completed - Settlement ID: {$settlement->id}, Success: " . ($emailSent ? 'Yes' : 'No'));
                    } catch (\Exception $e) {
                        Log::error("Failed to send settlement approval email - Settlement ID: {$settlement->id}, Error: " . $e->getMessage());
                        // Jangan rollback transaksi karena email, hanya log error
                    }
                }
            } elseif ($request->status === 'rejected') {
                // Jika rejected, settlement ditolak
                $settlement->status_settlement = 'rejected';
                Log::info("Settlement rejected - Settlement ID: {$settlement->id}");
            } elseif ($request->status === 'revision') {
                // Jika revision, settlement perlu diperbaiki
                $settlement->status_settlement = 'revision';
                Log::info("Settlement needs revision - Settlement ID: {$settlement->id}");
            }
    
            $settlement->save();
    
            Log::info("Settlement status updated - ID: {$settlement->id}, New Status: {$settlement->status_settlement}, Current Step: {$settlement->current_step}");
    
            // Buat history untuk settlement
            try {
                HistoryPengajuan::createHistory(
                    $pengajuan->id,
                    'settlement_approval',
                    $statusBefore,
                    $settlement->status_settlement,
                    $userId,
                    'Settlement diperbarui oleh approver: ' . $currentUser->nama,
                    $request->catatan,
                    'Settlement Step ' . $currentSettlementProgress->urutan,
                    $currentSettlementProgress->urutan,
                    $settlement->id
                );
            } catch (\Exception $e) {
                Log::warning("Failed to create settlement history: " . $e->getMessage());
                // Jangan rollback transaksi karena history, hanya log warning
            }
    
            DB::commit(); // Commit database transaction
    
            // Prepare response message
            $message = 'Status settlement berhasil diperbarui';
            if ($isLastLayer) {
                if ($emailSent) {
                    $message .= ' dan notifikasi email telah dikirim ke requester';
                } else {
                    $message .= ' namun gagal mengirim notifikasi email ke requester';
                }
            }
    
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'status' => $request->status,
                    'settlement_status' => $settlement->status_settlement,
                    'current_step' => $settlement->current_step,
                    'total_step' => $settlement->total_step,
                    'pengajuan_status' => $pengajuan->status_pengajuan,
                    'is_last_layer' => $isLastLayer,
                    'email_sent' => $emailSent
                ]
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating settlement status for pengajuan ID {$pengajuanId}: " . $e->getMessage(), [
                'user_id' => $userId,
                'pengajuan_id' => $pengajuanId,
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status settlement. Silakan coba lagi. Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function sendSettlementApprovedNotification($pengajuan, $settlement, $approver)
    {
        try {
            $requester = $pengajuan->requester;
            
            if (!$requester || !$requester->email) {
                Log::warning("Requester or email not found for pengajuan ID: " . $pengajuan->id);
                return false;
            }

            // Cek apakah email sudah pernah dikirim untuk settlement ini
            $existingNotification = $this->checkExistingEmailNotification($settlement->id, $requester->id);
            if ($existingNotification) {
                Log::info("Email notification already sent for settlement ID: " . $settlement->id);
                return true; // Return true karena email sudah dikirim sebelumnya
            }

            // Validasi template email exists
            $templatePath = resource_path('views/emails/settlement-approved.blade.php');
            if (!file_exists($templatePath)) {
                Log::error("Email template not found: " . $templatePath);
                return false;
            }

            // Data untuk email template
            $emailData = [
                'requester_name' => $requester->nama,
                'pengajuan_nomor' => $pengajuan->nomor_pengajuan,
                'pengajuan_judul' => $pengajuan->judul,
                'pengajuan_nominal' => $pengajuan->nominal_pengajuan,
                'settlement_nomor' => $settlement->nomor_settlement,
                'settlement_total' => $settlement->total_actual,
                'settlement_selisih' => $settlement->selisih,
                'mata_uang' => $pengajuan->mata_uang ?? 'IDR',
                'approver_name' => $approver->nama,
                'tanggal_approval' => now()->format('d/m/Y H:i'),
                'company_name' => config('app.name', 'Perusahaan'),
                'pengajuan' => $pengajuan, // Pass full object for additional data if needed
                'settlement' => $settlement, // Pass full object for additional data if needed
            ];

            // Kirim email menggunakan Laravel Mail
            Mail::send('emails.settlement-approved', $emailData, function ($message) use ($requester, $pengajuan, $settlement) {
                $message->to($requester->email, $requester->nama)
                        ->subject('Settlement Pengajuan Telah Disetujui - ' . $pengajuan->nomor_pengajuan)
                        ->priority(1); // High priority
            });

            // Log sukses
            $this->logEmailNotification(
                $pengajuan->id,
                $settlement->id,
                $requester->id,
                $requester->email,
                'success',
                'Settlement approved notification sent successfully'
            );

            Log::info("Settlement approval email sent successfully", [
                'settlement_id' => $settlement->id,
                'pengajuan_id' => $pengajuan->id,
                'requester_email' => $requester->email,
                'requester_name' => $requester->nama
            ]);
            
            return true;

        } catch (\Exception $e) {
            // Log email notification error
            $this->logEmailNotification(
                $pengajuan->id ?? null,
                $settlement->id ?? null,
                $requester->id ?? null,
                $requester->email ?? 'unknown',
                'failed',
                'Failed to send settlement approved notification',
                [
                    'error_message' => $e->getMessage(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                    'stack_trace' => $e->getTraceAsString()
                ]
            );

            Log::error("Failed to send settlement approval email", [
                'settlement_id' => $settlement->id ?? null,
                'pengajuan_id' => $pengajuan->id ?? null,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ]);
            
            return false;
        }
    }
    
    private function checkExistingEmailNotification($settlementId, $recipientId)
    {
        return EmailNotificationLog::where('settlement_id', $settlementId)
            ->where('recipient_id', $recipientId)
            ->where('status', 'success')
            ->where('message', 'like', '%Settlement approved notification%')
            ->exists();
    }
    
    /**
     * Log email notification attempt
     */
    private function logEmailNotification($pengajuanId, $settlementId, $recipientId, $recipientEmail, $status, $message, $errorDetails = null)
    {
        try {
            EmailNotificationLog::create([
                'pengajuan_id' => $pengajuanId,
                'settlement_id' => $settlementId,
                'recipient_id' => $recipientId,
                'recipient_email' => $recipientEmail,
                'status' => $status,
                'message' => $message,
                'sent_at' => now(),
                'error_details' => $errorDetails ? json_encode($errorDetails) : null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to log email notification: " . $e->getMessage());
        }
    }

    /**
     * Method untuk resend email notification (optional - for manual retry)
     */
    public function resendSettlementNotification($pengajuanId)
    {
        try {
            $userId = Auth::id();
            
            $pengajuan = Pengajuan::with(['requester'])->find($pengajuanId);
            if (!$pengajuan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan tidak ditemukan'
                ], 404);
            }

            // Find the approved settlement
            $settlement = Settlement::where('pengajuan_id', $pengajuanId)
                ->where('status_settlement', 'approved')
                ->first();
                
            if (!$settlement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Settlement yang sudah disetujui tidak ditemukan'
                ], 404);
            }

            $currentUser = Auth::user();
            $emailSent = $this->sendSettlementApprovedNotification($pengajuan, $settlement, $currentUser);

            if ($emailSent) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email notifikasi berhasil dikirim ulang'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim ulang email notifikasi'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error("Error resending settlement notification: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim ulang email notifikasi'
            ], 500);
        }
    }
     
    // update status untuk pengajuan
    public function updateStatus(Request $request, $id)
    {
        try {
            $userId = Auth::id();
            
            // Validasi input
            $request->validate([
                'status' => 'required|in:approved,rejected,revision',
                'catatan' => 'nullable|string|max:1000'
            ]);

            // Cari pengajuan dengan relasi requester
            $pengajuan = Pengajuan::with(['requester', 'kategoriPengajuan'])->find($id);
            if (!$pengajuan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan tidak ditemukan'
                ], 404);
            }

            $progressApproval = ProgressApproval::where('pengajuan_id', $id)
                ->where('approver_id', $userId)
                ->where('urutan', $pengajuan->current_step)
                ->first();

            if (!$progressApproval) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menyetujui pengajuan ini'
                ], 403);
            }

            // Simpan status sebelumnya untuk history
            $statusBefore = $pengajuan->status_pengajuan;
            $currentUser = Auth::user();

            // Update progress approval
            $progressApproval->update([
                'status' => $request->status,
                'catatan' => $request->catatan,
                'tanggal_approval' => now()
            ]);

            // Update pengajuan berdasarkan status
            if ($request->status === 'approved') {
                // Jika approved dan masih ada step selanjutnya
                if ($pengajuan->current_step < $pengajuan->total_step) {
                    $pengajuan->current_step += 1;
                    $pengajuan->status_pengajuan = 'proses'; // Status tetap proses jika masih ada step selanjutnya
                } else {
                    // Jika sudah step terakhir
                    $pengajuan->status_pengajuan = 'approved';
                }
            } else {
                // Jika rejected atau revision
                $pengajuan->status_pengajuan = $request->status;
            }

            $pengajuan->save();

            // Buat history pengajuan
            HistoryPengajuan::createHistory(
                $pengajuan->id,
                'status_update',
                $statusBefore,
                $pengajuan->status_pengajuan,
                $userId,
                'Status diperbarui oleh approver: ' . $currentUser->nama,
                $request->catatan,
                'Step ' . $progressApproval->urutan,
                $progressApproval->urutan
            );

            // Kirim notifikasi email ke requester
            $emailSent = $this->emailService->sendStatusUpdateNotification(
                $pengajuan,
                $request->status,
                $currentUser->nama,
                $request->catatan
            );

            $message = 'Status pengajuan berhasil diperbarui';
            if ($emailSent) {
                $message .= ' dan notifikasi email telah dikirim ke requester';
            } else {
                $message .= ', namun gagal mengirim notifikasi email';
                Log::warning("Email notification failed for pengajuan ID: {$pengajuan->id}");
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'status' => $request->status,
                    'current_step' => $pengajuan->current_step,
                    'total_step' => $pengajuan->total_step,
                    'email_sent' => $emailSent
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error("Error updating status for pengajuan ID {$id}: " . $e->getMessage(), [
                'user_id' => $userId,
                'pengajuan_id' => $id,
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status pengajuan. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Method untuk mengecek status pengajuan dan email logs
     */
    public function getEmailLogs($pengajuanId)
    {
        try {
            $pengajuan = Pengajuan::with('emailNotificationLogs')->find($pengajuanId);
            
            if (!$pengajuan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $pengajuan->emailNotificationLogs
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting email logs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data email logs'
            ], 500);
        }
    }

    /**
     * Method untuk mendapatkan status pengajuan
     */
    public function getStatus($id)
    {
        try {
            $userId = Auth::id();
            
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID pengajuan tidak valid'
                ], 400);
            }
            
            $pengajuan = Pengajuan::with(['progressApprovals' => function($query) use ($userId) {
                $query->where('approver_id', $userId);
            }])
            ->whereHas('progressApprovals', function($query) use ($userId) {
                $query->where('approver_id', $userId);
            })
            ->find($id);

            if (!$pengajuan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'status_pengajuan' => $pengajuan->status_pengajuan ?? 'pending',
                    'current_step' => $pengajuan->current_step ?? 1,
                    'total_step' => $pengajuan->total_step ?? 1,
                    'user_approval_status' => optional($pengajuan->progressApprovals->first())->status ?? 'pending'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Error in getStatus method: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil status pengajuan'
            ], 500);
        }
    }

    
    /**
     * Method helper untuk update status pengajuan keseluruhan
     */
    private function updateOverallPengajuanStatus($pengajuan)
    {
        try {
            // Ambil semua progress approval untuk pengajuan ini
            $allProgressApprovals = ProgressApproval::where('pengajuan_id', $pengajuan->id)
                ->orderBy('step')
                ->get();

            if ($allProgressApprovals->isEmpty()) {
                return;
            }

            // Cek apakah ada yang rejected
            if ($allProgressApprovals->contains('status', 'rejected')) {
                $pengajuan->update([
                    'status_pengajuan' => 'rejected'
                ]);
                return;
            }

            // Cek apakah ada yang revision
            if ($allProgressApprovals->contains('status', 'revision')) {
                $pengajuan->update([
                    'status_pengajuan' => 'revision'
                ]);
                return;
            }

            // Cek apakah semua sudah approved
            $totalApprovals = $allProgressApprovals->count();
            $approvedCount = $allProgressApprovals->where('status', 'approved')->count();

            if ($approvedCount === $totalApprovals) {
                $pengajuan->update([
                    'status_pengajuan' => 'approved',
                    'current_step' => $totalApprovals
                ]);
            } else {
                // Update current step
                $currentApprovedStep = $allProgressApprovals->where('status', 'approved')->max('step');
                if ($currentApprovedStep) {
                    $pengajuan->update([
                        'current_step' => $currentApprovedStep,
                        'status_pengajuan' => 'pending'
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error("Error in updateOverallPengajuanStatus: " . $e->getMessage());
            // Don't throw exception to avoid breaking the main flow
        }
    }
}