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
use App\Models\HistoryPengajuan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\Mail\FinanceInterventionNotification;
use App\Mail\SettlementRefundNotification;
use App\Mail\SettlementNearingCompletionMail;
use App\Mail\SettlementRefundReminderMail;
use App\Mail\SettlementRefundStatusMail;
use App\Mail\FinanceSettlementInterventionNotification;

use Illuminate\Support\Facades\Mail;
use App\Services\EmailNotificationService;
use Carbon\Carbon;

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
            // Ambil data pengajuan yang melibatkan user ini sebagai approver
            $pengajuanList = Pengajuan::with([
                'kategoriPengajuan',
                'requester.department',
                'detailPengajuan.formField',
                // Eager load progress approval KHUSUS untuk user ini agar bisa cek statusnya di view
                'progressApprovals' => function($query) use ($userId) {
                    $query->where('approver_id', $userId);
                }
            ])
            // Filter: Hanya ambil pengajuan di mana user ini TERDAFTAR sebagai approver (status apapun)
            ->whereHas('progressApprovals', function($query) use ($userId) {
                $query->where('approver_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
            // Hitung data pending (Global count, tidak berubah)
            $pendingPengajuan = \App\Models\Pengajuan::where('status_pengajuan', '!=', 'completed')->count();
            $pendingSettlement = \App\Models\Settlement::where('status_settlement', '!=', 'approved')->count();
            
            $totalPending = $pendingPengajuan + $pendingSettlement;

            return view('Approval-app.Karyawan.LaporanPengajuan.index', compact('pengajuanList', 'totalPending', 'pendingPengajuan', 'pendingSettlement'));
        } catch (\Exception $e) {
            Log::error('Error in LaporanPengajuanController@index: ' . $e->getMessage());
            return back()->withErrors('Terjadi kesalahan saat memuat data pengajuan.');
        }
    }
    
    public function getSettlementNotificationData($id)
    {
        try {
            $settlement = Settlement::with(['pengajuan.requester', 'pengajuan.kategoriPengajuan'])
                ->findOrFail($id);
            
            return response()->json([
                'nomor_settlement' => $settlement->nomor_settlement,
                'requester_name' => $settlement->pengajuan->requester->nama,
                'mata_uang' => $settlement->pengajuan->mata_uang,
                'refund_amount' => number_format(abs($settlement->selisih), 0, ',', '.')
            ]);
    
        } catch (\Exception $e) {
            Log::error('Error getting settlement notification data: ' . $e->getMessage());
            return response()->json(['error' => 'Settlement tidak ditemukan'], 404);
    
        } finally {
            // Cleanup atau logging tambahan jika diperlukan
            Log::info('Settlement notification data request completed for ID: ' . $id);
        }
    }
    
    public function kirimNotifikasiRefund(Request $request)
    {
        try {
            $settlementId = $request->input('settlement_id');
            
            // Validasi settlement ID
            if (!$settlementId) {
                return back()->withErrors('Settlement ID tidak ditemukan.');
            }

            // Ambil data settlement beserta pengajuan dan requester
            $settlement = Settlement::with(['pengajuan.requester'])->find($settlementId);
            
            if (!$settlement) {
                return back()->withErrors('Data settlement tidak ditemukan.');
            }

            if (!$settlement->pengajuan || !$settlement->pengajuan->requester) {
                return back()->withErrors('Data requester tidak ditemukan.');
            }

            $requester = $settlement->pengajuan->requester;
            $pengajuan = $settlement->pengajuan;

            // Pastikan ada sisa yang perlu dikembalikan
            if ($settlement->selisih <= 0) {
                return back()->withErrors('Tidak ada sisa dana yang perlu dikembalikan.');
            }

            // Kirim email notifikasi
            try {
                Mail::to($requester->email)->send(new SettlementRefundReminderMail($settlement, $pengajuan, $requester));
                
                // Log notifikasi email
                EmailNotificationLog::create([
                    'pengajuan_id' => $pengajuan->id,
                    'settlement_id' => $settlement->id,
                    'recipient_id' => $requester->id,
                    'recipient_email' => $requester->email,
                    'email_type' => 'settlement_refund_reminder',
                    'status' => 'success',
                    'message' => 'Notifikasi pengingat pengembalian sisa dana settlement berhasil dikirim',
                    'sent_at' => now(),
                    'retry_count' => 0
                ]);

                return back()->with('success', 'Notifikasi berhasil dikirim ke ' . $requester->nama . ' (' . $requester->email . ')');
                
            } catch (\Exception $emailError) {
                Log::error('Error sending settlement refund reminder email: ' . $emailError->getMessage());
                
                // Log email yang gagal
                EmailNotificationLog::create([
                    'pengajuan_id' => $pengajuan->id,
                    'settlement_id' => $settlement->id,
                    'recipient_id' => $requester->id,
                    'recipient_email' => $requester->email,
                    'email_type' => 'settlement_refund_reminder',
                    'status' => 'failed',
                    'message' => 'Gagal mengirim notifikasi pengingat pengembalian sisa dana settlement',
                    'error_details' => ['error' => $emailError->getMessage()],
                    'sent_at' => null,
                    'retry_count' => 0
                ]);

                return back()->withErrors('Gagal mengirim notifikasi email. Silakan coba lagi.');
            }

        } catch (\Exception $e) {
            Log::error('Error in kirimNotifikasiRefund: ' . $e->getMessage());
            return back()->withErrors('Terjadi kesalahan saat mengirim notifikasi.');
        }
    }

    /**
     * Method detail - Perbaikan utama dengan error handling yang lebih baik
     */
    public function detail($id)
    {
        try {
            $userId = Auth::id();
            $currentUser = Auth::user();
            
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
                'historyPengajuan',
                'detailPengajuan.formField',
                'progressApprovals' => function($query) {
                    $query->orderBy('urutan');
                },
                'progressApprovals.approver',
                'progressApprovals.approver.department',
                'historyPengajuan',
                'financeInterventionBy'
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
                $settlement = Settlement::with([
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
                                'nominal_awal' => $detail->detailPengajuan->nilai,
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
                    if ($detail && $detail->formField) {
                        $detailFields[] = [
                            'detail_id' => $detail->id, // TAMBAHAN: Include detail_id
                            'name' => $detail->formField->nama_field ?? 'unknown_field',
                            'label' => $detail->formField->label ?? 'Unknown Field',
                            'type' => $detail->formField->tipe_field ?? 'text',
                            'value' => $detail->nilai ?? '',
                            'jumlah_hari' => $detail->jumlah_hari,
                            'urutan' => $detail->formField->urutan ?? 999,
                            // TAMBAHAN: Info intervensi Finance
                            'is_intervened_by_finance' => $detail->is_intervened_by_finance ?? false,
                            'nilai_awal' => $detail->nilai_awal,
                            'finance_intervention_date' => $detail->finance_intervention_date,
                            'finance_intervention_by' => $detail->financeInterventionBy ? [
                                'nama' => $detail->financeInterventionBy->nama,
                                'department' => $detail->financeInterventionBy->department ? 
                                               $detail->financeInterventionBy->department->nama : '-'
                            ] : null
                        ];
                    }
                }
            }
        
            // Urutkan berdasarkan urutan field
            usort($detailFields, function($a, $b) {
                return $a['urutan'] <=> $b['urutan'];
            });
            
            $hotelMakanData = $this->calculateHotelMakanData($pengajuan->detailPengajuan);
    
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
            
            $pengajuan->detail_fields = $detailFields;
            $pengajuan->progress_data = $progressData;
            $pengajuan->hotel_makan_data = $hotelMakanData;
    
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
                               $currentUserProgress['is_current'],
                               
                
                'is_intervened_by_finance' => $pengajuan->is_intervened_by_finance ?? false,
                'catatan_intervensi_finance' => $pengajuan->catatan_intervensi_finance,
                'finance_intervention_date' => $pengajuan->finance_intervention_date,
                'finance_intervention_by' => $pengajuan->financeInterventionBy ? [
                    'nama' => $pengajuan->financeInterventionBy->nama,
                    'department' => $pengajuan->financeInterventionBy->department ? 
                                   $pengajuan->financeInterventionBy->department->nama : '-'
                ] : null,
                
                
                'is_intervened_by_finance' => $pengajuan->is_intervened_by_finance ?? false,
                'nominal_awal' => $pengajuan->nominal_awal,
                'mata_uang_awal' => $pengajuan->mata_uang_awal,
                'catatan_intervensi_finance' => $pengajuan->catatan_intervensi_finance,
                'finance_intervention_date' => $pengajuan->finance_intervention_date,
                
                // TAMBAHAN: Cek apakah user adalah Finance
                'is_finance_user' => $currentUser && $currentUser->department_id == 2,
                'can_intervene' => $currentUser && 
                                 $currentUser->department_id == 2 && 
                                 in_array($pengajuan->status_pengajuan, ['pending', 'proses']),
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

    // TAMBAHAN: Fungsi helper untuk menghitung data hotel dan makan dengan jumlah hari
    private function calculateHotelMakanData($detailPengajuan)
    {
        $hotelData = [];
        $makanData = [];
        
        foreach ($detailPengajuan as $detail) {
            if (!$detail->formField) continue;
            
            $fieldName = $detail->formField->nama_field;
            
            // Hotel data dengan jumlah malam
            if (strpos($fieldName, 'hotel_biaya') !== false) {
                $hotelData[$fieldName] = [
                    'rate_per_malam' => (float)$detail->nilai,
                    'jumlah_malam' => (int)$detail->jumlah_hari,
                    'total' => (float)$detail->nilai * (int)$detail->jumlah_hari
                ];
            }
            
            // Makan data dengan jumlah hari
            if (strpos($fieldName, 'makan_biaya') !== false) {
                $makanData[$fieldName] = [
                    'rate_per_hari' => (float)$detail->nilai,
                    'jumlah_hari' => (int)$detail->jumlah_hari,
                    'total' => (float)$detail->nilai * (int)$detail->jumlah_hari
                ];
            }
        }
        
        return [
            'hotel' => $hotelData,
            'makan' => $makanData
        ];
    }
    
    // public function updateDetailIntervention(Request $request, $id)
    // {
    //     try {
    //         $userId = Auth::id();
    //         $currentUser = Auth::user();
            
    //         // Validasi apakah user adalah Finance (department_id = 2)
    //         if (!$currentUser || $currentUser->department_id != 2) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Akses ditolak. Hanya departemen Finance yang dapat melakukan intervensi.'
    //             ], 403);
    //         }
            
    //         // Validasi input - array detail interventions
    //         $request->validate([
    //             'detail_interventions' => 'required|array|min:1',
    //             'detail_interventions.*.detail_id' => 'required|integer|exists:DetailPengajuan,id',
    //             'detail_interventions.*.nilai_final' => 'required|string',
    //             'catatan_intervensi' => 'required|string|max:1000'
    //         ]);
    
    //         // Cari pengajuan
    //         $pengajuan = Pengajuan::with(['detailPengajuan.formField', 'requester', 'kategoriPengajuan'])->find($id);
    //         if (!$pengajuan) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Pengajuan tidak ditemukan'
    //             ], 404);
    //         }
    
    //         // Validasi apakah pengajuan masih bisa di-intervensi
    //         if (!in_array($pengajuan->status_pengajuan, ['pending', 'proses'])) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Pengajuan ini tidak dapat diintervensi karena sudah dalam status: ' . $pengajuan->status_pengajuan
    //             ], 400);
    //         }
    
    //         DB::beginTransaction();
            
    //         try {
    //             $interventions = [];
    //             $nominalLama = $pengajuan->nominal_pengajuan;
                
    //             // Proses setiap detail intervention
    //             foreach ($request->detail_interventions as $intervention) {
    //                 $detailPengajuan = DetailPengajuan::where('id', $intervention['detail_id'])
    //                     ->where('pengajuan_id', $id)
    //                     ->with('formField')
    //                     ->first();
                    
    //                 if (!$detailPengajuan) {
    //                     throw new \Exception("Detail pengajuan dengan ID {$intervention['detail_id']} tidak ditemukan");
    //                 }
                    
    //                 // Simpan nilai awal jika belum ada
    //                 if (is_null($detailPengajuan->nilai_awal)) {
    //                     $detailPengajuan->nilai_awal = $detailPengajuan->nilai;
    //                 }
                    
    //                 // Update dengan nilai final dari Finance
    //                 $detailPengajuan->update([
    //                     'nilai' => $intervention['nilai_final'],
    //                     'is_intervened_by_finance' => true,
    //                     'finance_intervention_date' => now(),
    //                     'finance_intervention_by' => $userId
    //                 ]);
                    
    //                 // Simpan data untuk history dan response
    //                 $interventions[] = [
    //                     'detail_id' => $detailPengajuan->id,
    //                     'field_name' => $detailPengajuan->formField->label ?? 'Unknown Field',
    //                     'nilai_awal' => $detailPengajuan->nilai_awal,
    //                     'nilai_final' => $intervention['nilai_final']
    //                 ];
    //             }
                
    //             // KALKULASI ULANG NOMINAL PENGAJUAN
    //             $nominalBaruTotal = $this->calculateTotalNominalPengajuan($pengajuan);
                
    //             // Update pengajuan dengan flag intervensi dan nominal baru
    //             $pengajuan->update([
    //                 'nominal_pengajuan' => $nominalBaruTotal,
    //                 'is_intervened_by_finance' => true,
    //                 'finance_intervention_date' => now(),
    //                 'finance_intervention_by' => $userId,
    //                 'catatan_intervensi_finance' => $request->catatan_intervensi
    //             ]);
                
    //             // Buat history untuk setiap intervensi
    //             foreach ($interventions as $intervention) {
    //                 HistoryPengajuan::createHistory(
    //                     $pengajuan->id,
    //                     'finance_detail_intervention',
    //                     "Detail '{$intervention['field_name']}': {$intervention['nilai_awal']}",
    //                     "Detail '{$intervention['field_name']}': {$intervention['nilai_final']}",
    //                     $userId,
    //                     'Intervensi Finance - detail pengajuan diubah',
    //                     $request->catatan_intervensi,
    //                     'Finance Detail Intervention',
    //                     0 // urutan 0 untuk intervensi finance
    //                 );
    //             }
                
    //             // Buat history untuk perubahan nominal pengajuan
    //             if ($nominalLama != $nominalBaruTotal) {
    //                 HistoryPengajuan::createHistory(
    //                     $pengajuan->id,
    //                     'finance_nominal_recalculation',
    //                     "Nominal pengajuan: " . number_format($nominalLama, 2),
    //                     "Nominal pengajuan: " . number_format($nominalBaruTotal, 2),
    //                     $userId,
    //                     'Kalkulasi ulang nominal setelah intervensi Finance',
    //                     "Nominal berubah dari " . number_format($nominalLama, 2) . " menjadi " . number_format($nominalBaruTotal, 2),
    //                     'Nominal Recalculation',
    //                     0
    //                 );
    //             }
                
    //             DB::commit();
    
    //             // Kirim email notifikasi ke requester
    //             try {
    //                 Mail::to($pengajuan->requester->email)->send(
    //                     new FinanceInterventionNotification([
    //                         'pengajuan' => $pengajuan,
    //                         'interventions' => $interventions,
    //                         'finance_user' => $currentUser->nama,
    //                         'catatan_intervensi' => $request->catatan_intervensi,
    //                         'nominal_lama' => $nominalLama,
    //                         'nominal_baru' => $nominalBaruTotal,
    //                         'selisih_nominal' => $nominalBaruTotal - $nominalLama
    //                     ])
    //                 );
                
    //                 // Log email berhasil
    //                 EmailNotificationLog::create([
    //                     'pengajuan_id' => $pengajuan->id,
    //                     'recipient_id' => $pengajuan->requester_id,
    //                     'recipient_email' => $pengajuan->requester->email,
    //                     'status' => 'finance_intervention',
    //                     'message' => 'Email notifikasi intervensi finance berhasil dikirim',
    //                     'sent_at' => now(),
    //                 ]);
                    
    //                 $emailSent = true;
                
    //             } catch (\Exception $e) {
    //                 // Log email gagal
    //                 EmailNotificationLog::create([
    //                     'pengajuan_id' => $pengajuan->id,
    //                     'recipient_id' => $pengajuan->requester_id,
    //                     'recipient_email' => $pengajuan->requester->email,
    //                     // 'email_type' => 'finance_intervention',
    //                     'status' => 'failed',
    //                     'message' => 'Gagal mengirim email notifikasi intervensi finance',
    //                     'error_details' => [
    //                         'error_message' => $e->getMessage(),
    //                         'file' => $e->getFile(),
    //                         'line' => $e->getLine()
    //                     ],
    //                     // 'retry_count' => 0
    //                 ]);
                    
    //                 Log::error('Failed to send finance intervention email: ' . $e->getMessage());
    //                 $emailSent = false;
    //             }
                
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Detail pengajuan berhasil diubah oleh Finance',
    //                 'data' => [
    //                     'interventions' => $interventions,
    //                     'catatan_intervensi' => $request->catatan_intervensi,
    //                     'total_items_changed' => count($interventions),
    //                     'nominal_lama' => $nominalLama,
    //                     'nominal_baru' => $nominalBaruTotal,
    //                     'selisih_nominal' => $nominalBaruTotal - $nominalLama,
    //                     'email_sent' => $emailSent
    //                 ]
    //             ]);
    //         } catch (\Exception $e) {
    //             DB::rollback();
    //             throw $e;
    //         }
    
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Data tidak valid',
    //             'errors' => $e->errors()
    //         ], 422);
            
    //     } 
    // }
    public function updateDetailIntervention(Request $request, $id)
    {
        try {
            $userId = Auth::id();
            $currentUser = Auth::user();
            
            // Validasi apakah user adalah Finance (department_id = 2)
            if (!$currentUser || $currentUser->department_id != 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Hanya departemen Finance yang dapat melakukan intervensi.'
                ], 403);
            }
            
            // Validasi input - array detail interventions
            $request->validate([
                'detail_interventions' => 'required|array|min:1',
                'detail_interventions.*.detail_id' => 'required|integer|exists:DetailPengajuan,id',
                'detail_interventions.*.nilai_final' => 'required|string',
                'catatan_intervensi' => 'required|string|max:1000'
            ]);
    
            // Cari pengajuan
            $pengajuan = Pengajuan::with(['detailPengajuan.formField', 'requester', 'kategoriPengajuan'])->find($id);
            if (!$pengajuan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan tidak ditemukan'
                ], 404);
            }
    
            // Validasi apakah pengajuan masih bisa di-intervensi
            if (!in_array($pengajuan->status_pengajuan, ['pending', 'proses'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan ini tidak dapat diintervensi karena sudah dalam status: ' . $pengajuan->status_pengajuan
                ], 400);
            }
    
            DB::beginTransaction();
            
            try {
                $interventions = [];
                $nominalLama = $pengajuan->nominal_pengajuan;
                
                // Cek apakah ini adalah intervensi pertama
                $isFirstIntervention = !$pengajuan->is_intervened_by_finance;
                
                // Jika ini intervensi pertama, simpan nominal sebelum revisi
                if ($isFirstIntervention) {
                    $pengajuan->nominal_sblm_revisi = $nominalLama;
                }
                
                // Proses setiap detail intervention
                foreach ($request->detail_interventions as $intervention) {
                    $detailPengajuan = DetailPengajuan::where('id', $intervention['detail_id'])
                        ->where('pengajuan_id', $id)
                        ->with('formField')
                        ->first();
                    
                    if (!$detailPengajuan) {
                        throw new \Exception("Detail pengajuan dengan ID {$intervention['detail_id']} tidak ditemukan");
                    }
                    
                    // Simpan nilai awal jika belum ada
                    if (is_null($detailPengajuan->nilai_awal)) {
                        $detailPengajuan->nilai_awal = $detailPengajuan->nilai;
                    }
                    
                    // Update dengan nilai final dari Finance
                    $detailPengajuan->update([
                        'nilai' => $intervention['nilai_final'],
                        'is_intervened_by_finance' => true,
                        'finance_intervention_date' => now(),
                        'finance_intervention_by' => $userId
                    ]);
                    
                    // Simpan data untuk history dan response
                    $interventions[] = [
                        'detail_id' => $detailPengajuan->id,
                        'field_name' => $detailPengajuan->formField->label ?? 'Unknown Field',
                        'nilai_awal' => $detailPengajuan->nilai_awal,
                        'nilai_final' => $intervention['nilai_final']
                    ];
                }
                
                // KALKULASI ULANG NOMINAL PENGAJUAN
                $nominalBaruTotal = $this->calculateTotalNominalPengajuan($pengajuan);
                
                // Update pengajuan dengan flag intervensi dan nominal baru
                $updateData = [
                    'nominal_pengajuan' => $nominalBaruTotal,
                    'is_intervened_by_finance' => true,
                    'finance_intervention_date' => now(),
                    'finance_intervention_by' => $userId,
                    'catatan_intervensi_finance' => $request->catatan_intervensi
                ];
                
                // Tambahkan nominal_sblm_revisi hanya jika ini intervensi pertama
                if ($isFirstIntervention) {
                    $updateData['nominal_sblm_revisi'] = $nominalLama;
                }
                
                $pengajuan->update($updateData);
                
                // Buat history untuk setiap intervensi
                foreach ($interventions as $intervention) {
                    HistoryPengajuan::createHistory(
                        $pengajuan->id,
                        'finance_detail_intervention',
                        "Detail '{$intervention['field_name']}': {$intervention['nilai_awal']}",
                        "Detail '{$intervention['field_name']}': {$intervention['nilai_final']}",
                        $userId,
                        'Intervensi Finance - detail pengajuan diubah',
                        $request->catatan_intervensi,
                        'Finance Detail Intervention',
                        0 // urutan 0 untuk intervensi finance
                    );
                }
                
                // Buat history untuk perubahan nominal pengajuan
                if ($nominalLama != $nominalBaruTotal) {
                    $historyMessage = $isFirstIntervention ? 
                        'Kalkulasi ulang nominal setelah intervensi Finance (revisi pertama)' : 
                        'Kalkulasi ulang nominal setelah intervensi Finance (revisi lanjutan)';
                        
                    HistoryPengajuan::createHistory(
                        $pengajuan->id,
                        'finance_nominal_recalculation',
                        "Nominal pengajuan: " . number_format($nominalLama, 2),
                        "Nominal pengajuan: " . number_format($nominalBaruTotal, 2),
                        $userId,
                        $historyMessage,
                        "Nominal berubah dari " . number_format($nominalLama, 2) . " menjadi " . number_format($nominalBaruTotal, 2),
                        'Nominal Recalculation',
                        0
                    );
                }
                
                // Jika ini intervensi pertama, buat history khusus untuk menyimpan nominal sebelum revisi
                if ($isFirstIntervention) {
                    HistoryPengajuan::createHistory(
                        $pengajuan->id,
                        'first_intervention_backup',
                        "Nominal sebelum revisi: " . number_format($nominalLama, 2),
                        "Status: Disimpan untuk referensi",
                        $userId,
                        'Backup nominal sebelum intervensi pertama Finance',
                        'Nominal asli sebelum adanya intervensi Finance disimpan sebagai referensi',
                        'First Intervention Backup',
                        0
                    );
                }
                
                DB::commit();

                \App\Helpers\ActivityLogger::log('Finance Intervention', "Finance intervened on pengajuan {$pengajuan->nomor_pengajuan}");
    
                // Kirim email notifikasi ke requester
                try {
                    Mail::to($pengajuan->requester->email)->send(
                        new FinanceInterventionNotification([
                            'pengajuan' => $pengajuan,
                            'interventions' => $interventions,
                            'finance_user' => $currentUser->nama,
                            'catatan_intervensi' => $request->catatan_intervensi,
                            'nominal_lama' => $nominalLama,
                            'nominal_baru' => $nominalBaruTotal,
                            'selisih_nominal' => $nominalBaruTotal - $nominalLama,
                            'is_first_intervention' => $isFirstIntervention
                        ])
                    );
                
                    // Log email berhasil
                    EmailNotificationLog::create([
                        'pengajuan_id' => $pengajuan->id,
                        'recipient_id' => $pengajuan->requester_id,
                        'recipient_email' => $pengajuan->requester->email,
                        'status' => 'finance_intervention',
                        'message' => 'Email notifikasi intervensi finance berhasil dikirim',
                        'sent_at' => now(),
                    ]);
                    
                    $emailSent = true;
                
                } catch (\Exception $e) {
                    // Log email gagal
                    EmailNotificationLog::create([
                        'pengajuan_id' => $pengajuan->id,
                        'recipient_id' => $pengajuan->requester_id,
                        'recipient_email' => $pengajuan->requester->email,
                        'status' => 'failed',
                        'message' => 'Gagal mengirim email notifikasi intervensi finance',
                        'error_details' => [
                            'error_message' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine()
                        ],
                    ]);
                    
                    Log::error('Failed to send finance intervention email: ' . $e->getMessage());
                    $emailSent = false;
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Detail pengajuan berhasil diubah oleh Finance',
                    'data' => [
                        'interventions' => $interventions,
                        'catatan_intervensi' => $request->catatan_intervensi,
                        'total_items_changed' => count($interventions),
                        'nominal_lama' => $nominalLama,
                        'nominal_baru' => $nominalBaruTotal,
                        'selisih_nominal' => $nominalBaruTotal - $nominalLama,
                        'email_sent' => $emailSent,
                        'is_first_intervention' => $isFirstIntervention,
                        'nominal_sblm_revisi' => $pengajuan->nominal_sblm_revisi
                    ]
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
            
        } 
    }
   
    private function calculateTotalNominalPengajuan($pengajuan)
    {
        $totalNominal = 0;
        
        // Refresh data detail pengajuan untuk mendapatkan nilai terbaru
        $pengajuan->load('detailPengajuan.formField');
        
        // Hitung total nominal dengan mempertimbangkan jumlah_hari dari setiap detail
        foreach ($pengajuan->detailPengajuan as $detail) {
            $formField = $detail->formField;
            
            // Hanya hitung field yang bertipe currency atau number
            if ($formField && in_array($formField->tipe_field, ['currency', 'number'])) {
                $nilai = $detail->nilai;
                
                // Convert ke numeric jika berupa string
                if (is_string($nilai)) {
                    // Remove currency formatting jika ada (Rp, koma, dll)
                    $nilai = preg_replace('/[^\d.-]/', '', $nilai);
                }
                
                $nominalValue = is_numeric($nilai) ? floatval($nilai) : 0;
                
                // Skip jika nilai 0 atau negatif
                if ($nominalValue <= 0) {
                    continue;
                }
                
                // Identifikasi field berdasarkan nama untuk menentukan apakah perlu dikalikan dengan jumlah hari
                $fieldName = strtolower($formField->nama_field ?? $formField->label ?? '');
                
                // Field yang perlu dikalikan dengan jumlah hari (per hari/malam)
                $perHariFields = [
                    'uang_makan', 'uangmakan', 'makan',
                    'uang_hotel', 'uanghotel', 'hotel', 'penginapan',
                    'transport_harian', 'transportasi_harian',
                    'allowance_harian', 'tunjangan_harian'
                ];
                
                $isPerHariField = false;
                foreach ($perHariFields as $perHariField) {
                    if (strpos($fieldName, $perHariField) !== false) {
                        $isPerHariField = true;
                        break;
                    }
                }
                
                // Skip field yang berkaitan dengan total hari itu sendiri
                $isHariField = strpos($fieldName, 'total_hari') !== false || 
                              strpos($fieldName, 'lama_hari') !== false ||
                              strpos($fieldName, 'hari') !== false ||
                              $formField->identifier === 'total_hari';
                
                if ($isHariField) {
                    continue; // Skip field hari karena ini bukan nominal yang perlu dihitung
                }
                
                // Jika field adalah per hari/malam, kalikan dengan jumlah_hari dari detail tersebut
                if ($isPerHariField) {
                    $jumlahHari = $detail->jumlah_hari ?? 1; // Default 1 jika null
                    $totalNominal += $nominalValue * $jumlahHari;
                } else {
                    // Jika bukan per hari, tambahkan langsung
                    $totalNominal += $nominalValue;
                }
            }
        }
        
        return $totalNominal;
    }
    
    public function settlementDetail($pengajuanId)
    {
        try {
            $userId = Auth::id();
            $currentUser = Auth::user();
            
            Log::info("Loading settlement detail for pengajuan ID: {$pengajuanId}, User ID: {$userId}");
            
            if (!is_numeric($pengajuanId) || $pengajuanId <= 0) {
                return response()->json(['success' => false, 'message' => 'ID pengajuan tidak valid'], 400);
            }
            
            // ... (Bagian awal query pengajuan tetap sama) ...
            $pengajuan = Pengajuan::with([
                'kategoriPengajuan',
                'requester.department',
                'progressApprovals' => function($query) {
                    $query->orderBy('urutan')->with(['approver.department', 'settlement']);
                }
            ])
            ->whereHas('progressApprovals', function($query) use ($userId) {
                $query->where('approver_id', $userId)->whereNotNull('settlement_id');
            })
            ->find($pengajuanId);
    
            if (!$pengajuan) {
                return response()->json(['success' => false, 'message' => 'Pengajuan tidak ditemukan'], 404);
            }
    
            $settlementProgress = $pengajuan->progressApprovals()->whereNotNull('settlement_id')->first();
            if (!$settlementProgress || !$settlementProgress->settlement) {
                return response()->json(['success' => false, 'message' => 'Data settlement tidak ditemukan'], 404);
            }
    
            $settlement = $settlementProgress->settlement;
            
            // =================================================================
            // PERBAIKAN UTAMA DI SINI
            // Tambahkan 'details.detailPengajuan' agar nominal awal terbaca
            // =================================================================
            $settlement->load([
                'details.financeInterventionBy', 
                'details.detailPengajuan', // <--- INI YANG KURANG SEBELUMNYA
                'pengajuan'
            ]);
    
            // ... (Logika penentuan layer finance tetap sama) ...
            $settlementProgressApprovals = \App\Models\ProgressApproval::where('settlement_id', $settlement->id)
                ->with(['approver.department'])
                ->orderBy('urutan')
                ->get();

            // 1. Logika Layer Finance
            $allSteps = ProgressApproval::where('pengajuan_id', $pengajuanId)->with('approver')->orderBy('urutan')->get();
            $firstFinanceStep = $allSteps->first(function ($step) {
                return $step->approver && $step->approver->department_id == 2;
            });
            $isFinanceLayer1 = false;
            if ($currentUser->department_id == 2 && $firstFinanceStep) {
                $isFinanceLayer1 = ($settlement->current_step == $firstFinanceStep->urutan);
            }

            // 2. Mapping Data Details (Pastikan nominal_awal diambil dengan aman)
            $detailsData = $settlement->details->map(function($detail) {
                // Ambil nilai dari relasi, jika null set ke 0
                $nominalAwal = $detail->detailPengajuan ? $detail->detailPengajuan->nilai : 0;
                $jumlahHari = $detail->detailPengajuan ? $detail->detailPengajuan->jumlah_hari : 1; // Default 1

                return [
                    'id' => $detail->id,
                    'keterangan' => $detail->keterangan ?? '-',
                    'tanggal_transaksi' => $detail->tanggal_transaksi,
                    'nominal' => $detail->nominal ?? 0,
                    'kategori_biaya' => $detail->kategori_biaya,
                    'file_bukti' => $detail->file_bukti,
                    'catatan' => $detail->catatan,
                    
                    // PERBAIKAN: Pastikan data ini terisi
                    'nominal_awal' => $nominalAwal,
                    'jumlah_hari' => $jumlahHari,

                    'is_intervened_by_finance' => $detail->is_intervened_by_finance ?? false,
                    'finance_intervention_date' => $detail->finance_intervention_date,
                    'finance_intervention_by' => $detail->financeInterventionBy ? $detail->financeInterventionBy->nama : null,
                    'original_keterangan' => $detail->original_keterangan,
                    'original_nominal' => $detail->original_nominal,
                    'original_kategori_biaya' => $detail->original_kategori_biaya
                ];
            });
            
            // ... (Sisa kode progress data dan response tetap sama) ...
            // Transformasi data progress approval untuk timeline
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
            }

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
                    'total_Awal' => $settlement->pengajuan->nominal_pengajuan ?? 0,
                    'selisih' => $settlement->selisih ?? 0,
                    'status_settlement' => $settlement->status_settlement ?? 'pending',
                    'catatan_settlement' => $settlement->catatan_settlement,
                    'file_bukti_transfer' => $settlement->file_bukti_transfer,
                    'tanggal_transfer' => $settlement->tanggal_transfer,
                    'catatan_transfer' => $settlement->catatan_transfer,
                    'current_step' => $settlement->current_step,
                    'total_step' => $settlement->total_step ?? 1,
                    'status_realisasi' => $this->determineRefundStatus($settlement) === 'pengembalian_ke_perusahaan' ? 'under' : 'over', // Tambahan helper flag
                    'is_intervened_by_finance' => $settlement->is_intervened_by_finance ?? false,
                    'finance_intervention_date' => $settlement->finance_intervention_date,
                    'catatan_intervensi_finance' => $settlement->catatan_intervensi_finance
                ],
                'details' => $detailsData,
                'progress_data' => $progressData,
                'current_user_progress' => $currentUserProgress,
                'can_approve' => $currentUserProgress && 
                               in_array($currentUserProgress['status'], ['pending', 'proses']) && 
                               $currentUserProgress['is_current'],
                'is_finance_user' => $currentUser->department_id == 2,
                'is_finance_layer_1' => $isFinanceLayer1,
                'current_user' => [
                    'id' => $currentUser->id,
                    'nama' => $currentUser->nama,
                    'department_id' => $currentUser->department_id,
                    'department_name' => $currentUser->department ? $currentUser->department->nama : '-'
                ]
            ];
    
            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);
    
        } catch (\Exception $e) {
            Log::error("Error in settlementDetail: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    
    public function updateSettlementDetailIntervention(Request $request, $pengajuanId)
    {
        
        try {
            $userId = Auth::id();
            $currentUser = Auth::user();
            
            // Validasi apakah user adalah Finance (department_id = 2)
            if (!$currentUser || $currentUser->department_id != 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Hanya departemen Finance yang dapat melakukan intervensi.'
                ], 403);
            }
            
            // Validasi input
            $request->validate([
                'detail_interventions' => 'required|array|min:1',
                'detail_interventions.*.detail_settlement_id' => 'required|integer|exists:DetailSettlement,id',
                'detail_interventions.*.keterangan' => 'nullable|string',
                'detail_interventions.*.nominal' => 'required|numeric|min:0',
                'detail_interventions.*.kategori_biaya' => 'nullable|string',
                'catatan_intervensi' => 'required|string|max:1000'
            ]);
    
            // Cari pengajuan dan settlement
            $pengajuan = Pengajuan::with(['requester'])->find($pengajuanId);
            if (!$pengajuan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan tidak ditemukan'
                ], 404);
            }
    
            // Cari settlement melalui progress approval
            $settlementProgress = ProgressApproval::where('pengajuan_id', $pengajuanId)
                ->whereNotNull('settlement_id')
                ->first();
    
            if (!$settlementProgress || !$settlementProgress->settlement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Settlement tidak ditemukan'
                ], 404);
            }
    
            $settlement = $settlementProgress->settlement;
    
            // Validasi apakah settlement masih bisa di-intervensi
            if (!in_array($settlement->status_settlement, ['pending', 'proses'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Settlement ini tidak dapat diintervensi karena sudah dalam status: ' . $settlement->status_settlement
                ], 400);
            }
    
            DB::beginTransaction();
            
            try {
                $interventions = [];
                $totalActualLama = $settlement->total_actual;
                $newTotalActual = 0;
                
                // Proses setiap detail intervention
                foreach ($request->detail_interventions as $intervention) {
                    $detailSettlement = DetailSettlement::where('id', $intervention['detail_settlement_id'])
                        ->where('settlement_id', $settlement->id)
                        ->first();
                    
                    if (!$detailSettlement) {
                        throw new \Exception("Detail settlement dengan ID {$intervention['detail_settlement_id']} tidak ditemukan");
                    }
                    
                    // Simpan nilai awal jika belum ada
                    $originalData = [
                        'keterangan' => $detailSettlement->keterangan,
                        'nominal' => $detailSettlement->nominal,
                        'kategori_biaya' => $detailSettlement->kategori_biaya
                    ];
                    
                    // Update detail settlement
                    $detailSettlement->update([
                        'keterangan' => $intervention['keterangan'] ?? $detailSettlement->keterangan,
                        'nominal' => $intervention['nominal'],
                        'kategori_biaya' => $intervention['kategori_biaya'] ?? $detailSettlement->kategori_biaya,
                        // Tambahkan field untuk tracking intervensi
                        'is_intervened_by_finance' => true,
                        'finance_intervention_date' => now(),
                        'finance_intervention_by' => $userId,
                        'original_keterangan' => $originalData['keterangan'],
                        'original_nominal' => $originalData['nominal'],
                        'original_kategori_biaya' => $originalData['kategori_biaya']
                    ]);
                    
                    // Simpan data untuk history dan response
                    $interventions[] = [
                        'detail_settlement_id' => $detailSettlement->id,
                        'keterangan_awal' => $originalData['keterangan'],
                        'keterangan' => $detailSettlement->keterangan,
                        'keterangan_final' => $intervention['keterangan'] ?? $detailSettlement->keterangan,
                        'nominal_awal' => $originalData['nominal'],
                        'nominal_final' => $intervention['nominal'],
                        'kategori_awal' => $originalData['kategori_biaya'],
                        'kategori_final' => $intervention['kategori_biaya'] ?? $detailSettlement->kategori_biaya
                    ];
                }
                
                // Hitung ulang total actual settlement
                $newTotalActual = DetailSettlement::where('settlement_id', $settlement->id)
                    ->sum('nominal');
                
                // Update settlement dengan total baru
                $selisihBaru = $pengajuan->nominal_pengajuan - $newTotalActual;
                
                $settlement->update([
                    'total_actual' => $newTotalActual,
                    'selisih' => $selisihBaru,
                    'is_intervened_by_finance' => true,
                    'finance_intervention_date' => now(),
                    'finance_intervention_by' => $userId,
                    'catatan_intervensi_finance' => $request->catatan_intervensi
                ]);
                
                // Buat history untuk setiap intervensi
                foreach ($interventions as $intervention) {
                    HistoryPengajuan::createHistory(
                        $pengajuan->id,
                        'finance_settlement_intervention',
                        "Settlement detail - Nominal: {$intervention['nominal_awal']}",
                        "Settlement detail - Nominal: {$intervention['nominal_final']}",
                        $userId,
                        'Intervensi Finance pada detail settlement',
                        $request->catatan_intervensi,
                        'Finance Settlement Intervention',
                        0, // urutan 0 untuk intervensi finance
                        $settlement->id
                    );
                }
                
                // Buat history untuk perubahan total actual
                if ($totalActualLama != $newTotalActual) {
                    HistoryPengajuan::createHistory(
                        $pengajuan->id,
                        'finance_settlement_recalculation',
                        "Total actual: " . number_format($totalActualLama, 2),
                        "Total actual: " . number_format($newTotalActual, 2),
                        $userId,
                        'Kalkulasi ulang total actual setelah intervensi Finance',
                        "Total berubah dari " . number_format($totalActualLama, 2) . " menjadi " . number_format($newTotalActual, 2),
                        'Settlement Recalculation',
                        0,
                        $settlement->id
                    );
                }
                
                DB::commit();

                \App\Helpers\ActivityLogger::log('Finance Settlement Intervention', "Finance intervened on settlement for pengajuan {$pengajuan->nomor_pengajuan}");
                
                try {
                    Mail::to($pengajuan->requester->email)->send(
                        new FinanceSettlementInterventionNotification([
                            'pengajuan' => $pengajuan,
                            'settlement' => $settlement,
                            'interventions' => $interventions,
                            'finance_user' => $currentUser->nama,
                            'catatan_intervensi' => $request->catatan_intervensi,
                            'total_actual_lama' => $totalActualLama,
                            'total_actual_baru' => $newTotalActual,
                            'selisih_lama' => $pengajuan->nominal_pengajuan - $totalActualLama,
                            'selisih_baru' => $selisihBaru,
                            'total_items_changed' => count($interventions)
                        ])
                    );
                
                    // Log email berhasil
                    EmailNotificationLog::create([
                        'pengajuan_id' => $pengajuan->id,
                        'recipient_id' => $pengajuan->requester_id,
                        'recipient_email' => $pengajuan->requester->email,
                        'status' => 'finance_settlement_intervention',
                        'message' => 'Email notifikasi intervensi settlement finance berhasil dikirim',
                        'sent_at' => now(),
                    ]);
                    
                    $emailSent = true;
                
                } catch (\Exception $e) {
                    // Log email gagal
                    EmailNotificationLog::create([
                        'pengajuan_id' => $pengajuan->id,
                        'recipient_id' => $pengajuan->requester_id,
                        'recipient_email' => $pengajuan->requester->email,
                        'status' => 'failed',
                        'message' => 'Gagal mengirim email notifikasi intervensi settlement finance',
                        'error_details' => [
                            'error_message' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine()
                        ],
                    ]);
                    
                    Log::error('Failed to send finance settlement intervention email: ' . $e->getMessage());
                    $emailSent = false;
                }
                
                // Update response data untuk menambahkan status email
                return response()->json([
                    'success' => true,
                    'message' => 'Detail settlement berhasil diubah oleh Finance',
                    'data' => [
                        'interventions' => $interventions,
                        'catatan_intervensi' => $request->catatan_intervensi,
                        'total_items_changed' => count($interventions),
                        'total_actual_lama' => $totalActualLama,
                        'total_actual_baru' => $newTotalActual,
                        'selisih_lama' => $pengajuan->nominal_pengajuan - $totalActualLama,
                        'selisih_baru' => $selisihBaru,
                        'email_sent' => $emailSent // Tambahkan ini
                    ]
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error in updateSettlementDetailIntervention: ' . $e->getMessage(), [
                'pengajuan_id' => $pengajuanId,
                'user_id' => $userId,
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat melakukan intervensi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update status settlement (Approval)
     */
    public function updateSettlementStatus(Request $request, $pengajuanId)
    {
        DB::beginTransaction(); // Start database transaction
        
        try {
            $userId = Auth::id();
            $currentUser = Auth::user();
            
            // 1. Validasi input
            $request->validate([
                'status' => 'required|in:approved,rejected,revision',
                'catatan' => 'nullable|string|max:1000'
            ]);

            Log::info("Settlement approval attempt - User ID: {$userId}, Pengajuan ID: {$pengajuanId}, Status: {$request->status}");

            // 2. Cari pengajuan dengan relasi
            $pengajuan = Pengajuan::with(['requester', 'kategoriPengajuan'])->find($pengajuanId);
            if (!$pengajuan) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan tidak ditemukan'
                ], 404);
            }

            // 3. Verifikasi Hak Akses: Ambil settlement melalui progress approval user ini
            // Cek apakah user punya akses ke settlement di pengajuan ini
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

            // =========================================================================
            // LOGIKA VALIDASI DINAMIS (Finance Layer 2+ Blocking)
            // =========================================================================
            
            // A. Ambil semua step approval untuk mencari tahu urutan Finance
            $allSteps = ProgressApproval::where('pengajuan_id', $pengajuanId)
                ->with('approver')
                ->orderBy('urutan')
                ->get();

            // B. Cari step (urutan) pertama kali Finance muncul (Layer 1)
            // Asumsi: Department ID 2 adalah Finance
            $firstFinanceStep = $allSteps->first(function ($step) {
                return $step->approver && $step->approver->department_id == 2; 
            });
            
            $firstFinanceStepUrutan = $firstFinanceStep ? $firstFinanceStep->urutan : 0;

            // C. Cek apakah User adalah Finance Layer 2 ke atas (Verifikator)
            // Logic: Dept ID = Finance DAN Step Sekarang > Step Finance Pertama
            $isFinanceLayer2Plus = ($currentUser->department_id == 2) && ($settlement->current_step > $firstFinanceStepUrutan);

            // D. JALANKAN BLOCKING HANYA UNTUK FINANCE LAYER 2+
            if ($request->status === 'approved' && $isFinanceLayer2Plus) {
                // Cek Sisa Dana & Bukti Transfer
                if ($settlement->selisih > 0 && empty($settlement->file_bukti_transfer)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal Approval! Sebagai Finance Verifikator (Layer Lanjutan), Anda tidak dapat menyetujui settlement ini karena belum ada bukti transfer pengembalian dana dari Requester.'
                    ], 422); // 422 Unprocessable Entity
                }
            }
            // =========================================================================

            // 4. Cari progress approval spesifik untuk step saat ini
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

            // 5. Validasi status step saat ini (cegah double approval)
            if (!in_array($currentSettlementProgress->status, ['pending', 'proses'])) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Settlement sudah diproses sebelumnya'
                ], 409);
            }

            // Simpan status sebelumnya untuk history log
            $statusBefore = $settlement->status_settlement;

            // 6. Update progress approval user
            $currentSettlementProgress->update([
                'status' => $request->status,
                'catatan' => $request->catatan,
                'tanggal_approval' => now()
            ]);

            Log::info("Settlement progress updated - ID: {$currentSettlementProgress->id}, Status: {$request->status}");

            // Variables untuk response dan email logic
            $isLastLayer = false;
            $emailSent = false;
            $nearingCompletionEmailSent = false;

            // 7. Logika Workflow Settlement (Maju Step / Selesai / Tolak / Revisi)
            if ($request->status === 'approved') {
                // Jika approved dan masih ada step selanjutnya
                if ($settlement->current_step < $settlement->total_step) {
                    $settlement->current_step += 1;
                    $settlement->status_settlement = 'proses'; // Tetap proses
                    Log::info("Settlement moved to next step - Settlement ID: {$settlement->id}, New Step: {$settlement->current_step}");
                    
                    // CEK APAKAH MENDEKATI COMPLETION (< 3 STEPS REMAINING)
                    // Opsional: Logic notifikasi "nearing completion" ke requester agar siap-siap transfer
                    $stepsRemaining = $settlement->total_step - $settlement->current_step;
                    if ($stepsRemaining === 2) {
                        try {
                            $nearingCompletionEmailSent = $this->sendSettlementNearingCompletionNotification($pengajuan, $settlement, $currentUser, $stepsRemaining);
                        } catch (\Exception $e) {
                            Log::error("Failed to send nearing completion email: " . $e->getMessage());
                        }
                    }

                } else {
                    // Jika sudah step terakhir (LAYER TERAKHIR SELESAI)
                    $isLastLayer = true;
                    $settlement->status_settlement = 'approved';
                    
                    // Update pengajuan asli menjadi completed (Flow Selesai)
                    $pengajuan->status_pengajuan = 'completed';
                    $pengajuan->save();
                    
                    Log::info("Settlement fully approved and Pengajuan completed - ID: {$pengajuan->id}");
                    
                    // KIRIM EMAIL NOTIFIKASI KE REQUESTER BAHWA SETTLEMENT SELESAI
                    try {
                        $emailSent = $this->sendSettlementApprovedNotification($pengajuan, $settlement, $currentUser);
                    } catch (\Exception $e) {
                        Log::error("Failed to send settlement approval email: " . $e->getMessage());
                    }
                }

            } elseif ($request->status === 'rejected') {
                // Jika rejected, settlement ditolak total
                $settlement->status_settlement = 'rejected';
                Log::info("Settlement rejected - Settlement ID: {$settlement->id}");

            } elseif ($request->status === 'revision') {
                // Jika revision, kembalikan status untuk diperbaiki
                $settlement->status_settlement = 'revision';
                Log::info("Settlement needs revision - Settlement ID: {$settlement->id}");
            }

            $settlement->save();

            // 8. Buat History Log
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
            }

            DB::commit(); // Commit semua perubahan database

            \App\Helpers\ActivityLogger::log('Update Settlement Status', "User updated settlement status to {$request->status} for pengajuan {$pengajuan->nomor_pengajuan}");

            // 9. Siapkan Pesan Response
            $message = 'Status settlement berhasil diperbarui';
            if ($isLastLayer) {
                if ($emailSent) {
                    $message .= ' dan notifikasi email telah dikirim ke requester';
                } else {
                    $message .= ' namun gagal mengirim notifikasi email ke requester';
                }
            } elseif ($nearingCompletionEmailSent) {
                $message .= ' dan notifikasi mendekati penyelesaian telah dikirim ke requester';
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
                    'email_sent' => $emailSent,
                    'steps_remaining' => $settlement->total_step - $settlement->current_step
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
                'user_id' => $userId ?? 'unknown',
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status settlement. Silakan coba lagi. Error: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Send notification to requester when settlement is nearing completion
     */
    private function sendSettlementNearingCompletionNotification($pengajuan, $settlement, $approver, $stepsRemaining)
    {
        try {
            $requester = $pengajuan->requester;
            
            if (!$requester || !$requester->email) {
                Log::warning("Cannot send nearing completion email - requester or email not found for pengajuan ID: {$pengajuan->id}");
                return false;
            }
    
            // Determine refund status based on settlement selisih
            $refundStatus = $this->determineRefundStatus($settlement);
            
            // Data untuk email
            $emailData = [
                'requester_name' => $requester->nama,
                'nomor_pengajuan' => $pengajuan->nomor_pengajuan,
                'nomor_settlement' => $settlement->nomor_settlement,
                'kategori_pengajuan' => $pengajuan->kategoriPengajuan->nama ?? 'N/A',
                'total_actual' => number_format($settlement->total_actual, 0, ',', '.'),
                'selisih' => number_format(abs($settlement->selisih), 0, ',', '.'),
                'refund_status' => $refundStatus,
                'steps_remaining' => $stepsRemaining,
                'current_step' => $settlement->current_step,
                'total_step' => $settlement->total_step,
                'approver_name' => $approver->nama,
                'tanggal_settlement' => $settlement->tanggal_settlement->format('d F Y'),
                'app_url' => config('app.url')
            ];
    
            // Kirim email
            Mail::to($requester->email)->send(new SettlementNearingCompletionMail($emailData));
    
            // Log email notification
            $this->logEmailNotification(
                $pengajuan->id,
                $settlement->id,
                $requester->id,
                $requester->email,
                'settlement_nearing_completion',
                'success',
                'Email notifikasi settlement mendekati penyelesaian berhasil dikirim'
            );
    
            return true;
    
        } catch (\Exception $e) {
            Log::error("Error sending nearing completion email notification: " . $e->getMessage());
            
            // Log failed email notification
            $this->logEmailNotification(
                $pengajuan->id,
                $settlement->id,
                $requester->id ?? null,
                $requester->email ?? 'unknown',
                'settlement_nearing_completion',
                'failed',
                'Gagal mengirim email notifikasi settlement mendekati penyelesaian',
                $e->getMessage()
            );
    
            return false;
        }
    }
    
    /**
     * Determine refund status based on settlement selisih
     */
    private function determineRefundStatus($settlement)
    {
        if ($settlement->selisih < 0) {
            return 'pengembalian_ke_requester'; // Ada lebihan yang harus dikembalikan ke requester
        } elseif ($settlement->selisih > 0) {
            return 'pengembalian_ke_perusahaan'; // Ada kekurangan yang harus dibayar ke perusahaan
        } else {
            return 'balance'; // Tidak ada selisih, sudah balance
        }
    }
    
    /**
     * Log email notification to database
     */
    private function logEmailNotification($pengajuanId, $settlementId, $recipientId, $recipientEmail, $emailType, $status, $message, $errorDetails = null)
    {
        try {
            EmailNotificationLog::create([
                'pengajuan_id' => $pengajuanId,
                'settlement_id' => $settlementId,
                'recipient_id' => $recipientId,
                'recipient_email' => $recipientEmail,
                'email_type' => $emailType,
                'status' => $status,
                'message' => $message,
                'error_details' => $errorDetails ? ['error' => $errorDetails] : null,
                'sent_at' => $status === 'success' ? now() : null,
                'retry_count' => 0
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to log email notification: " . $e->getMessage());
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
            $this->logEmailNotification( //ini baris ke 1451
                $pengajuan->id,
                $settlement->id,
                $requester->id,
                $requester->email,
                'success',
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
                    $pengajuan->status_pengajuan = 'proses'; // Status tetap proses jika masih ada step selanjutnya
                    $emailSent = $this->emailService->sendStatusUpdateNotification(
                        $pengajuan,
                        $request->status,
                        $currentUser->nama,
                        $request->catatan
                    );
                    $pengajuan->current_step += 1;
                } else {
                    // Jika sudah step terakhir
                    $pengajuan->status_pengajuan = 'proses_settlement';
                    $emailSent = $this->emailService->sendStatusUpdateNotification(
                        $pengajuan,
                        $request->status,
                        $currentUser->nama,
                        $request->catatan
                    );
                }
            } else {
                // Jika rejected atau revision
                $pengajuan->status_pengajuan = $request->status;
                $emailSent = $this->emailService->sendStatusUpdateNotification(
                    $pengajuan,
                    $request->status,
                    $currentUser->nama,
                    $request->catatan
                );
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