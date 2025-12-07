<?php
namespace App\Http\Controllers\Employments;
use App\Http\Controllers\Controller;
use App\Models\TransactionRequestGroup;
use App\Models\TransactionRequest;
use App\Models\Pengajuan;
use App\Models\Karyawan;
use App\Models\Settlement;
use Carbon\Carbon;
use App\Models\KategoriPengajuan; // Tambahkan ini
use App\Models\Department; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Services\FinancePaymentService;
use App\Mail\SettlementStatusNotificationMail;

class TransactionRequestController extends Controller
{
    protected $financePaymentService;

    public function __construct(FinancePaymentService $financePaymentService)
    {
        $this->financePaymentService = $financePaymentService;
    }
    
   public function index()
    {
        // Ambil data TR Groups dengan settlement yang sudah ditambahkan
        $trGroups = TransactionRequestGroup::with([
            'pengajuans.requester',
            'pengajuans.kategoriPengajuan',
            'transactionRequests.pengajuan.requester',
            'transactionRequests.pengajuan.kategoriPengajuan',
            // TAMBAHAN: Include settlement dalam eager loading
            'transactionRequests.settlement.pengajuan.requester',
            'transactionRequests.settlement.pengajuan.kategoriPengajuan',
            'settlements.pengajuan.requester', // Relasi langsung ke settlements
            'settlements.pengajuan.kategoriPengajuan',
            'createdBy',
            'processedBy'
        ])->orderBy('created_at', 'desc')->get();
        
        $availableSettlement = Settlement::with(['pengajuan.requester.department', 'pengajuan.kategoriPengajuan'])
            ->where('selisih', '<', 0)
            ->whereIn('status_settlement', ['approved'])
            ->whereDoesntHave('transactionRequest')
            ->get();
    
        $availablePengajuan = Pengajuan::with(['requester.department', 'kategoriPengajuan'])
            ->whereIn('status_pengajuan', ['approved', 'proses_settlement'])
            ->whereDoesntHave('transactionRequest')
            ->get();
    
        // Update categories untuk include settlement
        $categories = KategoriPengajuan::where(function($query) {
            $query->whereHas('pengajuan', function($q) {
                $q->whereIn('status_pengajuan', ['approved', 'proses_settlement'])
                  ->whereDoesntHave('transactionRequest');
            })->orWhereHas('pengajuan.settlement', function($q) {
                $q->where('selisih', '<', 0)
                  ->whereIn('status_settlement', ['approved'])  // TAMBAHAN: filter status settlement
                  ->whereDoesntHave('transactionRequest');
            });
        })->get();
    
        $departments = Department::whereHas('karyawan.pengajuan', function($query) {
            $query->whereIn('status_pengajuan', ['approved', 'proses_settlement'])
                  ->whereDoesntHave('transactionRequest');
        })->orWhereHas('karyawan.pengajuan.settlement', function($query) {
            // TAMBAHAN: Include department yang punya settlement
            $query->where('selisih', '<', 0)
                  ->whereIn('status_settlement', ['approved'])
                  ->whereDoesntHave('transactionRequest');
        })->get();
        
        $countReadyPengajuan = Pengajuan::whereIn('status_pengajuan', ['approved', 'proses_settlement'])
            ->whereDoesntHave('transactionRequest')
            ->count();
        
        $countReadySettlement = Settlement::where('selisih', '<', 0)
            ->where('status_settlement', 'approved')
            ->whereDoesntHave('transactionRequest')
            ->count();

        $totalReadyToTR = $countReadyPengajuan + $countReadySettlement;
    
        return view('Approval-app.Karyawan.TransactionRequest.index', compact('trGroups', 'totalReadyToTR', 'availablePengajuan','availableSettlement', 'categories', 'departments'));
    }
    
    public function getDetailPengajuanFull($id)
    {
        try {
            $pengajuan = Pengajuan::with([
                'kategoriPengajuan',
                'requester.department',
                'detailPengajuan.formField',
                'settlement'
            ])->find($id);
    
            if (!$pengajuan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan tidak ditemukan'
                ], 404);
            }
    
            return response()->json([
                'success' => true,
                'data' => $pengajuan
            ]);
    
        } catch (\Exception $e) {
            \Log::error('Error getting detail pengajuan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail pengajuan'
            ], 500);
        }
    }

    // Method untuk filter pengajuan berdasarkan kategori dan department
    public function filterPengajuan(Request $request)
    {
        $query = Pengajuan::with(['requester.department', 'kategoriPengajuan'])
            ->where('status_pengajuan', 'approved')
            ->whereDoesntHave('transactionRequest');

        if ($request->filled('category_id')) {
            $query->where('kategori_pengajuan_id', $request->category_id);
        }

        if ($request->filled('department_id')) {
            $query->whereHas('requester', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $filteredPengajuan = $query->get();

        // Generate auto notes berdasarkan kategori yang dipilih
        $autoNotes = '';
        if ($request->filled('category_id')) {
            $category = KategoriPengajuan::find($request->category_id);
            if ($category) {
                $autoNotes = 'TR ' . $category->nama;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $filteredPengajuan,
            'auto_notes' => $autoNotes
        ]);
    }

    public function createTR(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pengajuan_ids' => 'nullable|array',
            'pengajuan_ids.*' => 'exists:Pengajuan,id',
            'settlement_ids' => 'nullable|array',
            'settlement_ids.*' => 'exists:Settlement,id',
            'notes' => 'nullable|string|max:1000'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Fix: Handle null values dengan default empty array
        $pengajuanIds = $request->pengajuan_ids ?? [];
        $settlementIds = $request->settlement_ids ?? [];
        
        $totalItems = count($pengajuanIds) + count($settlementIds);
        if ($totalItems === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih minimal satu pengajuan atau settlement'
            ], 400);
        }
    
        try {
            DB::beginTransaction();
    
            $pengajuans = collect(); // Initialize as empty collection
            $settlements = collect(); // Initialize as empty collection
    
            // Cek pengajuan hanya jika ada yang dipilih
            if (!empty($pengajuanIds)) {
                $pengajuans = Pengajuan::whereIn('id', $pengajuanIds)
                    ->whereIn('status_pengajuan', ['approved', 'proses_settlement'])
                    ->whereDoesntHave('transactionRequest')
                    ->get();
    
                // Fix: Compare dengan count yang sudah safe
                if ($pengajuans->count() !== count($pengajuanIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Beberapa pengajuan tidak valid atau sudah diproses'
                    ], 400);
                }
            }
            
            // Cek settlement hanya jika ada yang dipilih
            if (!empty($settlementIds)) {
                $settlements = Settlement::whereIn('id', $settlementIds)
                    ->whereDoesntHave('transactionRequest')
                    ->get();
                    
                // Validasi settlement juga
                if ($settlements->count() !== count($settlementIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Beberapa settlement tidak valid atau sudah diproses'
                    ], 400);
                }
            }
    
            // Buat TR Group baru
            $trGroup = TransactionRequestGroup::create([
                'tr_number' => TransactionRequestGroup::generateTRNumber(),
                'status' => 'pending',
                'notes' => $request->notes,
                'created_by' => Auth::id()
            ]);
    
            // Buat Transaction Request untuk setiap pengajuan
            foreach ($pengajuans as $pengajuan) {
                TransactionRequest::create([
                    'pengajuan_id' => $pengajuan->id,
                    'tr_group_id' => $trGroup->id,
                    'status' => 'waiting',
                    'processed_by' => Auth::id()
                ]);
    
                // Update status pembayaran pengajuan
                $pengajuan->update([
                    'statuspembayaran' => 'Menunggu'
                ]);
            }
            
            // Buat Transaction Request untuk setiap settlement
            foreach ($settlements as $settlement) {
                TransactionRequest::create([
                    'settlement_id' => $settlement->id,
                    'tr_group_id' => $trGroup->id,
                    'status' => 'waiting',
                    'processed_by' => Auth::id()
                ]);
            }
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Transaction Request berhasil dibuat',
                'tr_number' => $trGroup->tr_number
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat TR: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get detail TR untuk modal dengan data transaction requests
    public function getTRDetail($id)
    {
        try {
            $trGroup = TransactionRequestGroup::with([
                'transactionRequests.pengajuan.requester.department',
                'transactionRequests.pengajuan.kategoriPengajuan',
                'transactionRequests.settlement.pengajuan.requester.department',  // Tambahkan ini
                'transactionRequests.settlement.pengajuan.kategoriPengajuan',     // Tambahkan ini
                'transactionRequests.pengajuan.detailPengajuan.formField',
                'transactionRequests.processedBy',
                'createdBy',
                'processedBy'
            ])->findOrFail($id);
    
            return response()->json([
                'success' => true,
                'data' => $trGroup
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail TR'
            ], 500);
        }
    }
    
    public function getDetailSettlement($id)
    {
        try {
            $settlement = Settlement::with([
                'pengajuan.requester.department',
                'pengajuan.kategoriPengajuan',
                'details'
            ])->find($id);
    
            if (!$settlement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Settlement tidak ditemukan'
                ], 404);
            }
    
            return response()->json([
                'success' => true,
                'data' => $settlement
            ]);
    
        } catch (\Exception $e) {
            \Log::error('Error getting detail settlement: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail settlement'
            ], 500);
        }
    }

    // Update status individual pengajuan dalam TR
    public function updateStatus(Request $request, $pengajuanId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:waiting,paid,rejected',
            'catatan_finance' => 'nullable|string|max:1000',
            'bukti_transfer' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'tanggal_transfer' => 'required_if:status,paid|date'
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
    
            // Load pengajuan with requester relationship
            $pengajuan = Pengajuan::with(['requester', 'kategoriPengajuan'])->findOrFail($pengajuanId);
            $transactionRequest = TransactionRequest::where('pengajuan_id', $pengajuanId)->first();
    
            if (!$transactionRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction Request tidak ditemukan'
                ], 404);
            }
    
            $buktiTransferPath = $transactionRequest->bukti_transfer;
            $currentUser = Auth::user();
    
            // Handle file upload jika status = paid
            if ($request->status === 'paid' && $request->hasFile('bukti_transfer')) {
                // Hapus file lama jika ada
                if ($buktiTransferPath) {
                    Storage::disk('custom_public')->delete($buktiTransferPath);
                }
    
                $file = $request->file('bukti_transfer');
                $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $folderPath = 'assets/pengajuan/tr/' . 
                    str_replace(' ', '_', $pengajuan->requester->nama) . '/' .
                    str_replace(' ', '_', $pengajuan->kategoriPengajuan->nama);
                $buktiTransferPath = $file->storeAs($folderPath, $fileName, 'custom_public');
            }
    
            // Store old status for comparison
            $oldStatus = $transactionRequest->status;
    
            // Update Transaction Request
            $transactionRequest->update([
                'status' => $request->status,
                'catatan_finance' => $request->catatan_finance,
                'bukti_transfer' => $buktiTransferPath,
                'tanggal_transfer' => $request->tanggal_transfer,
                'processed_by' => Auth::id()
            ]);
    
            // Update status pembayaran pengajuan
            $statusPembayaran = match($request->status) {
                'waiting' => 'Menunggu',
                'paid' => 'Dibayarkan',
                'rejected' => 'Ditolak'
            };
    
            // **TAMBAHAN: Set argo date ketika status berubah menjadi 'paid' dan bukti transfer diupload**
            $argoDate = null;
            if ($request->status === 'paid' && ($request->hasFile('bukti_transfer') || $buktiTransferPath)) {
                // Hitung tanggal argo 21 hari dari hari ini (H+21 dari upload bukti transfer)
                $argoDate = Carbon::now()->addDays(21)->format('Y-m-d');
            }
    
            $pengajuan->update([
                'status' => $statusPembayaran,
                'argo' => $argoDate // Set argo date jika status paid dan ada bukti transfer
            ]);
    
            // Update status TR Group berdasarkan status semua pengajuan di dalamnya
            $trGroup = $transactionRequest->trGroup;
            $trGroup->updateStatus();
    
            // Send email notification to requester if status changed
            if ($oldStatus !== $request->status) {
                $emailSent = $this->financePaymentService->sendPaymentStatusNotification(
                    $pengajuan,
                    $transactionRequest,
                    $request->status,
                    $currentUser->nama,
                    $request->catatan_finance
                );
    
                Log::info("Payment status updated for pengajuan {$pengajuan->nomor_pengajuan}", [
                    'pengajuan_id' => $pengajuan->id,
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'processed_by' => $currentUser->nama,
                    'email_sent' => $emailSent,
                    'argo_date' => $argoDate // Log argo date untuk tracking
                ]);
            }
    
            DB::commit();
    
            $message = 'Status pembayaran berhasil diperbarui';
            if (isset($emailSent) && $emailSent) {
                $message .= ' dan notifikasi email telah dikirim ke requester';
            } elseif (isset($emailSent) && !$emailSent) {
                $message .= ', namun gagal mengirim notifikasi email';
            }
    
            // Tambahkan informasi argo dalam message jika ada
            if ($argoDate) {
                $message .= '. Periode argo settlement telah diaktifkan hingga ' . Carbon::parse($argoDate)->format('d/m/Y');
            }
    
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'status' => $request->status,
                    'status_text' => $this->financePaymentService->getPaymentStatusText($request->status),
                    'tanggal_transfer' => $request->tanggal_transfer,
                    'has_bukti_transfer' => !empty($buktiTransferPath),
                    'email_sent' => $emailSent ?? false,
                    'argo_date' => $argoDate // Return argo date dalam response
                ]
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            Log::error("Error updating payment status for pengajuan ID {$pengajuanId}: " . $e->getMessage(), [
                'user_id' => Auth::id(),
                'pengajuan_id' => $pengajuanId,
                'request_data' => $request->except(['bukti_transfer']), // Exclude file from log
                'stack_trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status pembayaran. Silakan coba lagi.'
            ], 500);
        }
    }
    
    public function updateSettlementStatus(Request $request, $settlementId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:proses,paid,rejected',
            'catatan_finance' => 'nullable|string|max:1000',
            'bukti_transfer' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'tanggal_transfer' => 'required_if:status,paid|date'
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
    
            // Load settlement dengan pengajuan
            $settlement = Settlement::with(['pengajuan.requester', 'pengajuan.kategoriPengajuan'])->findOrFail($settlementId);
            $transactionRequest = TransactionRequest::where('settlement_id', $settlementId)->first();
    
            if (!$transactionRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction Request tidak ditemukan'
                ], 404);
            }
    
            $buktiTransferPath = $transactionRequest->bukti_transfer;
            $currentUser = Auth::user();
    
            // Handle file upload jika status = paid
            if ($request->status === 'paid' && $request->hasFile('bukti_transfer')) {
                // Hapus file lama jika ada
                if ($buktiTransferPath) {
                    Storage::disk('custom_public')->delete($buktiTransferPath);
                }
    
                $file = $request->file('bukti_transfer');
                $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $folderPath = 'assets/settlement/tr/' . 
                             str_replace(' ', '_', $settlement->pengajuan->requester->nama) . '/' .
                             str_replace(' ', '_', $settlement->pengajuan->kategoriPengajuan->nama);
                $buktiTransferPath = $file->storeAs($folderPath, $fileName, 'custom_public');
            }
    
            // Store old status for comparison
            $oldStatus = $transactionRequest->status;
    
            // Update Transaction Request
            $transactionRequest->update([
                'status' => $request->status,
                'catatan_finance' => $request->catatan_finance,
                'bukti_transfer' => $buktiTransferPath,
                'tanggal_transfer' => $request->tanggal_transfer,
                'processed_by' => Auth::id()
            ]);
    
            // Update settlement transfer fields jika dibayar
            if ($request->status === 'paid') {
                $settlement->update([
                    'file_bukti_transfer' => $buktiTransferPath,
                    'tanggal_transfer' => $request->tanggal_transfer,
                    'catatan_transfer' => $request->catatan_finance
                ]);
            }
    
            // Update status TR Group berdasarkan status semua item di dalamnya
            $trGroup = $transactionRequest->trGroup;
            $trGroup->updateStatus();
    
            // Send email notification to requester if status changed
            $emailSent = false;
            if ($oldStatus !== $request->status) {
                $emailSent = $this->financePaymentService->sendSettlementStatusNotification(
                    $settlement,
                    $transactionRequest,
                    $request->status,
                    $currentUser->nama,
                    $request->catatan_finance
                );
    
                Log::info("Settlement status updated for settlement {$settlement->nomor_settlement}", [
                    'settlement_id' => $settlement->id,
                    'pengajuan_id' => $settlement->pengajuan_id,
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'processed_by' => $currentUser->nama,
                    'email_sent' => $emailSent
                ]);
            }
    
            DB::commit();
    
            $message = 'Status pembayaran settlement berhasil diperbarui';
            if (isset($emailSent) && $emailSent) {
                $message .= ' dan notifikasi email telah dikirim ke requester';
            } elseif (isset($emailSent) && !$emailSent) {
                $message .= ', namun gagal mengirim notifikasi email';
            }
    
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'status' => $request->status,
                    'status_text' => $this->financePaymentService->getPaymentStatusText($request->status),
                    'tanggal_transfer' => $request->tanggal_transfer,
                    'has_bukti_transfer' => !empty($buktiTransferPath),
                    'email_sent' => $emailSent ?? false
                ]
            ]);
    
        } catch (\Exception $e) {
        DB::rollBack();

        Log::error("Error updating settlement payment status for settlement ID {$settlementId}: " . $e->getMessage(), [
            'user_id' => Auth::id(),
            'settlement_id' => $settlementId,
            'request_data' => $request->except(['bukti_transfer']),
            'error_message' => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString()
        ]);

        // Determine specific error message
        $errorMessage = 'Gagal memperbarui status pembayaran settlement';
        
        if (str_contains($e->getMessage(), 'email') || str_contains($e->getMessage(), 'Email')) {
            $errorMessage .= ': Masalah pengiriman email - ' . $e->getMessage();
        } elseif (str_contains($e->getMessage(), 'file') || str_contains($e->getMessage(), 'upload')) {
            $errorMessage .= ': Masalah upload file - ' . $e->getMessage();
        } elseif (str_contains($e->getMessage(), 'database') || str_contains($e->getMessage(), 'SQL')) {
            $errorMessage .= ': Masalah database - ' . $e->getMessage();
        } else {
            $errorMessage .= ': ' . $e->getMessage();
        }

        return response()->json([
            'success' => false,
            'message' => $errorMessage,
            'error_details' => [
                'error_type' => get_class($e),
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        ], 500);
    }
    }

    // Download bukti transfer untuk pengajuan individual
    public function downloadBuktiTransfer($pengajuanId)
    {
        try {
            $transactionRequest = TransactionRequest::where('pengajuan_id', $pengajuanId)->first();
            
            if (!$transactionRequest || !$transactionRequest->bukti_transfer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bukti transfer tidak ditemukan'
                ], 404);
            }

            $filePath = storage_path('app/public/' . $transactionRequest->bukti_transfer);
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan di server'
                ], 404);
            }

            $fileName = 'Bukti_Transfer_' . $transactionRequest->pengajuan->nomor_pengajuan . '_' . basename($filePath);
            
            return response()->download($filePath, $fileName);

        } catch (\Exception $e) {
            Log::error('Error downloading bukti transfer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh bukti transfer'
            ], 500);
        }
    }
    // public function downloadBuktiPengajuan($pengajuanId)
    // {
    //     try {
    //         $transactionRequest = TransactionRequest::where('pengajuan_id', $pengajuanId)->first();

    //         if (!$transactionRequest || !$transactionRequest->bukti_transfer) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'File bukti transfer tidak ditemukan'
    //             ], 404);
    //         }

    //         $filePath = storage_path('app/public/' . $transactionRequest->bukti_transfer);

    //         if (!file_exists($filePath)) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'File tidak ditemukan di server'
    //             ], 404);
    //         }

    //         return response()->download($filePath);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal mengunduh file'
    //         ], 500);
    //     }
    // }

    // Delete TR (hanya yang masih pending)
    public function deleteTR($id)
    {
        try {
            DB::beginTransaction();

            $trGroup = TransactionRequestGroup::findOrFail($id);

            if ($trGroup->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya TR dengan status pending yang dapat dihapus'
                ], 400);
            }

            // Update pengajuan kembali ke status belum ada TR
            foreach ($trGroup->transactionRequests as $tr) {
                $tr->pengajuan->update([
                    'statuspembayaran' => "Menunggu"
                ]);
            }

            // Hapus transaction requests
            $trGroup->transactionRequests()->delete();

            // Hapus TR group
            $trGroup->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction Request berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus TR: ' . $e->getMessage()
            ], 500);
        }
    }

    // Legacy methods untuk backward compatibility
    public function getDetailPengajuan($id)
    {
        try {
            $pengajuan = Pengajuan::with([
                'requester.department',
                'kategoriPengajuan',
                'detailPengajuan.formField',
                'transactionRequest'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $pengajuan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail pengajuan'
            ], 500);
        }
    }

    // Method untuk download bukti transfer TR (legacy - untuk TR yang sudah completed)
    public function downloadBuktiPengajuan($pengajuanId)
    {
        try {
            $transactionRequest = TransactionRequest::where('pengajuan_id', $pengajuanId)->first();
            
            if (!$transactionRequest || !$transactionRequest->bukti_transfer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bukti transfer tidak ditemukan'
                ], 404);
            }
    
            $disk = 'custom_public';
            $filePath = null;
            
            // Cari file di berbagai lokasi
            if (Storage::disk($disk)->exists($transactionRequest->bukti_transfer)) {
                $filePath = Storage::disk($disk)->path($transactionRequest->bukti_transfer);
            } else {
                $possiblePaths = [
                    storage_path('app/public/' . $transactionRequest->bukti_transfer),
                    storage_path('app/' . $transactionRequest->bukti_transfer),
                ];
                
                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        $filePath = $path;
                        break;
                    }
                }
            }
            
            if (!$filePath || !file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan di server'
                ], 404);
            }
            
            // PERBAIKAN SEDERHANA: Buat nama file yang aman
            $originalExtension = pathinfo($filePath, PATHINFO_EXTENSION);
            $nomorPengajuan = $transactionRequest->pengajuan->nomor_pengajuan ?? 'Unknown';
            
            // Bersihkan nomor pengajuan dari karakter berbahaya
            $cleanNomorPengajuan = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $nomorPengajuan);
            
            // Nama file aman dengan timestamp untuk memastikan unik
            $safeFileName = 'Bukti_Transfer_' . $cleanNomorPengajuan . '_' . date('YmdHis') . '.' . $originalExtension;
            
            return response()->download($filePath, $safeFileName);
            
        } catch (\Exception $e) {
            \Log::error('Error downloading bukti pengajuan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh bukti transfer'
            ], 500);
        }
    }

    // Legacy method untuk complete TR (tidak digunakan lagi)
    public function completeTR(Request $request, $id)
    {
        // Method ini dipertahankan untuk backward compatibility
        // Tapi sekarang tidak digunakan karena pembayaran dilakukan per pengajuan
        
        return response()->json([
            'success' => false,
            'message' => 'Method ini sudah tidak digunakan. Gunakan pembayaran per pengajuan.'
        ], 400);
    }
}