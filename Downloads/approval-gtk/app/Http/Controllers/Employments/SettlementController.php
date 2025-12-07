<?php
namespace App\Http\Controllers\Employments;

use App\Http\Controllers\Controller;
use App\Models\Settlement;
use App\Models\DetailSettlement;
use App\Models\Pengajuan;
use App\Models\ProgressApproval;
use App\Models\EmailNotificationLog;
use App\Models\HistoryPengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Services\SettlementNotificationService;


class SettlementController extends Controller
{
    public function index()
    {
        $settlements = Settlement::with(['pengajuan.requester', 'pengajuan.kategoriPengajuan', 'details'])
            ->whereHas('pengajuan', function($query) {
                $query->where('requester_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Hitung data belum completed
        $pendingPengajuan = \App\Models\Pengajuan::where('status_pengajuan', '!=', 'completed')->count();
        $pendingSettlement = \App\Models\Settlement::where('status_settlement', '!=', 'approved')->count();
        
        // Pass ke view
        $totalPending = $pendingPengajuan + $pendingSettlement;

        return view('Approval-app.Karyawan.Settlement.index', compact('settlements', 'totalPending', 'pendingPengajuan', 'pendingSettlement'));
    }
    
    public function uploadBuktiTransfer(Request $request, $id)
    {
        try {
            $settlement = Settlement::with('pengajuan')->findOrFail($id);
            
            // Validasi bahwa settlement milik user yang login
            if ($settlement->pengajuan->requester_id !== Auth::id()) {
                return response()->json(['error' => 'Tidak memiliki akses untuk settlement ini'], 403);
            }
            
            // Validasi bahwa settlement memerlukan pengembalian
            // if ($settlement->status_realisasi !== 'under' || $settlement->selisih <= 0) {
            //     return response()->json(['error' => 'Settlement tidak memerlukan pengembalian'], 400);
            // }
            
            // Validasi input
            $validator = Validator::make($request->all(), [
                'tanggal_transfer' => 'required|date',
                'file_bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
                'catatan_transfer' => 'nullable|string|max:1000'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Data tidak valid',
                    'details' => $validator->errors()
                ], 422);
            }
            
            DB::beginTransaction();
            
            // Upload file
            $file = $request->file('file_bukti_transfer');
            $fileName = 'bukti_transfer_' . $settlement->nomor_settlement . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('settlements/bukti_transfer', $fileName, 'custom_public');
            
            // Update settlement
            $settlement->update([
                'file_bukti_transfer' => $filePath,
                'tanggal_transfer' => $request->tanggal_transfer,
                'catatan_transfer' => $request->catatan_transfer,
                'status_realisasi' => 'balance', // Update status menjadi balance setelah upload bukti
                'updated_at' => now()
            ]);
            
            // Log history
            HistoryPengajuan::create([
                'pengajuan_id' => $settlement->pengajuan_id,
                'settlement_id' => $settlement->id,
                'actor_id' => Auth::id(),
                'actor_name'  => Auth::user()->nama,
                'action' => 'upload_bukti_transfer',
                'description' => 'Upload bukti transfer pengembalian sisa dana sebesar ' . 
                               $settlement->pengajuan->mata_uang . ' ' . 
                               number_format(abs($settlement->selisih), 0, ',', '.'),
                'old_data' => null,
                'new_data' => [
                    'file_bukti_transfer' => $filePath,
                    'tanggal_transfer' => $request->tanggal_transfer,
                    'catatan_transfer' => $request->catatan_transfer
                ]
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => 'Bukti transfer berhasil diupload',
                'settlement' => $settlement->fresh()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Upload error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
        }
    }



public function submit($settlementId)
{
    DB::beginTransaction();
    try {
        $settlement = Settlement::with(['pengajuan.kategoriPengajuan.flowApprovals' => function($query) {
            $query->orderBy('urutan', 'asc');
        }])->findOrFail($settlementId);

        // Pastikan settlement punya status draft
        if ($settlement->status_settlement !== 'draft') {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Settlement tidak dapat diajukan karena statusnya bukan draft'
            ], 400);
        }

        $pengajuan = $settlement->pengajuan;

        // ✅ Filter FlowApproval hanya untuk user yang sedang login
        $currentUserId = Auth::user()->id; // kalau langsung pakai Auth::id() sesuaikan dengan field
        $flowApprovals = $pengajuan->kategoriPengajuan
            ->flowApprovals()
            ->where('requester_id', $currentUserId) 
            ->orderBy('urutan', 'asc')
            ->get();

        if ($flowApprovals->isEmpty()) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Flow approval belum dikonfigurasi untuk user ini pada kategori ini'
            ], 400);
        }

        // Cek duplikasi progress
        $existingProgressCount = ProgressApproval::where('settlement_id', $settlement->id)->count();
        if ($existingProgressCount > 0) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Progress approval untuk settlement ini sudah ada'
            ], 400);
        }

        // Lock settlement
        $settlement->lockForUpdate();

        // Recheck setelah lock
        $reCheckExisting = ProgressApproval::where('settlement_id', $settlement->id)->count();
        if ($reCheckExisting > 0) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Progress approval sudah dibuat oleh proses lain'
            ], 400);
        }

        // Buat progress approval
        $createdProgressIds = [];
        foreach ($flowApprovals as $flow) {
            $progressApproval = ProgressApproval::create([
                'pengajuan_id'   => $pengajuan->id,
                'settlement_id'  => $settlement->id,
                'flow_approval_id' => $flow->id,
                'requester_id'   => $currentUserId, // ✅ diikat ke user login
                'approver_id'    => $flow->approver_id,
                'step_name'      => $flow->nama_step, // pakai field sesuai model
                'urutan'         => $flow->urutan,
                'status'         => $flow->urutan == 1 ? 'pending' : 'proses',
                'step_type'      => $flow->step_type ?? 'approval',
                'tanggal_approval' => null,
                'catatan'        => null,
            ]);

            $createdProgressIds[] = $progressApproval->id;
        }

        // Update settlement
        $settlement->update([
            'status_settlement' => 'proses',
            'current_step'      => 1,
            'total_step'        => $flowApprovals->count()
        ]);

        DB::commit();

        // Kirim notifikasi email
        $notificationService = new SettlementNotificationService();
        $notificationService->sendSubmissionNotifications($settlement);

        return response()->json([
            'success' => true,
            'message' => 'Settlement berhasil diajukan',
            'created_progress' => $createdProgressIds
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Settlement submission error', [
            'settlement_id' => $settlementId,
            'error' => $e->getMessage()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengajukan settlement: ' . $e->getMessage()
        ], 500);
    }
}


    public function create($pengajuanId)
    {
        $pengajuan = Pengajuan::with([
            'settlement.details', 
            'kategoriPengajuan', 
            'detailPengajuan.formField'
        ])->findOrFail($pengajuanId);
        
        // Validasi apakah user berhak membuat settlement
        if ($pengajuan->requester_id != Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk membuat settlement ini.');
        }
    
        // Validasi apakah pengajuan sudah disetujui semua pihak
        if (!in_array($pengajuan->status_pengajuan, ['proses_settlement', 'disetujui'])) {
            return redirect()->back()->with('error', 'Settlement hanya dapat dibuat setelah pengajuan disetujui semua pihak.');
        }
    
        // Jika settlement sudah ada, redirect ke edit
        if ($pengajuan->settlement) {
            return redirect()->route('settlement.edit', $pengajuan->settlement->id)
                ->with('info', 'Settlement sudah dibuat. Anda dapat mengedit settlement yang ada.');
        }
    
        // PERBAIKAN: Hitung nilai original yang sudah dikalikan jumlah hari
        $calculatedDetails = $this->calculateOriginalValues($pengajuan);
    
        return view('Approval-app.Karyawan.Settlement.create', compact('pengajuan', 'calculatedDetails'));
    }
    
    private function calculateOriginalValues($pengajuan)
    {
        $calculatedDetails = [];
        $totalOriginal = 0;
    
        // Proses setiap detail pengajuan tipe currency
        foreach ($pengajuan->detailPengajuan as $detail) {
            if ($detail->formField->tipe_field == 'currency') {
                $originalValue = (float) $detail->nilai;
                
                if ($originalValue <= 0) {
                    continue;
                }
                
                $label = $detail->formField->label;
                $labelLower = strtolower($label);
                
                // GUNAKAN jumlah_hari dari DetailPengajuan langsung
                $jumlahHari = $detail->jumlah_hari ?? 1; // Ambil dari kolom jumlah_hari di DetailPengajuan
                $jumlahHari = max(1, $jumlahHari); // Minimal 1 hari
    
                // Tentukan apakah perlu dikalikan dengan jumlah hari
                $needsMultiplication = false;
                if (strpos($labelLower, 'hotel') !== false || 
                    strpos($labelLower, 'penginapan') !== false ||
                    strpos($labelLower, 'akomodasi') !== false ||
                    strpos($labelLower, 'makan') !== false || 
                    strpos($labelLower, 'konsumsi') !== false ||
                    strpos($labelLower, 'meal') !== false ||
                    strpos($labelLower, 'uang_harian') !== false) {
                    $needsMultiplication = true;
                }
    
                // Hitung nilai final
                if ($needsMultiplication && $jumlahHari > 1) {
                    $calculatedValue = $originalValue * $jumlahHari;
                } else {
                    $calculatedValue = $originalValue;
                    $jumlahHari = 1; // Reset untuk item yang tidak perlu dikalikan
                }
    
                $calculatedDetails[] = [
                    'form_field_id' => $detail->form_field_id,
                    'detail_id' => $detail->id,
                    'label' => $label,
                    'original_per_unit' => $originalValue,
                    'jumlah_hari' => $jumlahHari,
                    'calculated_value' => $calculatedValue,
                    'needs_multiplication' => $needsMultiplication
                ];
    
                $totalOriginal += $calculatedValue;
    
                // Debug log
                \Log::info("Detail: {$label} - Original: {$originalValue} - JumlahHari: {$jumlahHari} - Calculated: {$calculatedValue} - Needs Multiplication: " . ($needsMultiplication ? 'Yes' : 'No'));
            }
        }
    
        return [
            'details' => $calculatedDetails,
            'total_original' => $totalOriginal
        ];
    }

    public function store(Request $request, $pengajuanId)
    {
        $pengajuan = Pengajuan::with(['detailPengajuan.formField'])->findOrFail($pengajuanId);
        
        // Validasi
        if ($pengajuan->requester_id != Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        if ($pengajuan->settlement) {
            return response()->json(['error' => 'Settlement sudah ada'], 400);
        }
    
        // PERBAIKAN: Hitung total original yang benar
        $calculatedData = $this->calculateOriginalValues($pengajuan);
        $totalOriginal = $calculatedData['total_original'];
    
        // Validasi form
        $validationRules = [
            'details.*.actual_amount' => 'required|numeric|min:0',
            'details.*.file_bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'catatan_settlement' => 'nullable|string|max:1000'
        ];
        
        // Calculate total actual
        $totalActual = 0;
        if ($request->details) {
            foreach ($request->details as $detail) {
                $totalActual += (float) ($detail['actual_amount'] ?? 0);
            }
        }
        
        $selisih = $totalOriginal - $totalActual; // Menggunakan total original yang sudah benar
        
        // Jika ada sisa (selisih > 0), wajibkan upload bukti transfer
        // if ($selisih > 0) {
        //     $validationRules['file_bukti_transfer'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:5120';
        //     $validationRules['tanggal_transfer'] = 'required|date|before_or_equal:today';
        //     $validationRules['catatan_transfer'] = 'nullable|string|max:500';
        // }
    
        $request->validate($validationRules);
    
        DB::beginTransaction();
        try {
            // Persiapkan nama karyawan dan kategori pengajuan yang sudah dibersihkan
            $namaKaryawan = Auth::user()->nama ?? Auth::user()->name;
            $namaKaryawan = preg_replace('/[^a-zA-Z0-9_-]/', '_', $namaKaryawan);
            
            $kategoriPengajuan = $pengajuan->kategori ?? $pengajuan->jenis_pengajuan ?? 'umum';
            $kategoriPengajuan = preg_replace('/[^a-zA-Z0-9_-]/', '_', $kategoriPengajuan);
    
            // Handle upload file bukti transfer (jika ada)
            $fileBuktiTransferPath = null;
            if ($request->hasFile('file_bukti_transfer')) {
                $transferPath = "assets/karyawan/settlement/transfer_sisa/{$kategoriPengajuan}/{$namaKaryawan}";
                $fileBuktiTransferPath = $request->file('file_bukti_transfer')
                    ->store($transferPath, 'custom_public');
            }
    
            // Buat settlement
            $settlement = Settlement::create([
                'pengajuan_id' => $pengajuan->id,
                'nomor_settlement' => $this->generateNomorSettlement($pengajuan),
                'tanggal_settlement' => now(),
                'status_settlement' => 'draft',
                'catatan_settlement' => $request->catatan_settlement,
                'file_bukti_transfer' => $fileBuktiTransferPath,
                'tanggal_transfer' => $request->tanggal_transfer,
                'catatan_transfer' => $request->catatan_transfer,
                'current_step' => 1,
                'total_step' => 1
            ]);
    
            // Simpan detail settlement dengan mapping yang benar
            // foreach ($request->details as $index => $detailData) {
            //     // Cari detail pengajuan berdasarkan form_field_id
            //     $detailPengajuan = $pengajuan->detailPengajuan()
            //         ->where('form_field_id', $detailData['form_field_id'])
            //         ->first();
    
            //     if (!$detailPengajuan) {
            //         continue;
            //     }
    
            //     $filePath = null;
            //     if (isset($detailData['file_bukti']) && $detailData['file_bukti']) {
            //         $kategoriDetail = $detailPengajuan->formField->label ?? 'uncategorized';
            //         $kategoriDetail = preg_replace('/[^a-zA-Z0-9_-]/', '_', $kategoriDetail);
            //         $detailPath = "assets/karyawan/settlement/{$kategoriPengajuan}/{$namaKaryawan}/{$kategoriDetail}";
            //         $filePath = $detailData['file_bukti']->store($detailPath, 'custom_public');
            //     }
    
            //     $actualAmount = (float) $detailData['actual_amount'];
    
            //     DetailSettlement::create([
            //         'settlement_id' => $settlement->id,
            //         'form_field_id' => $detailData['form_field_id'],
            //         'detail_pengajuan_id' => $detailPengajuan->id,
            //         'keterangan' => $detailData['label'],
            //         'nominal' => $actualAmount,
            //         'file_bukti' => $filePath,
            //         'catatan' => $detailData['catatan'] ?? null,
            //         'tanggal_transaksi' => now()
            //     ]);
            // }
            
            foreach ($request->details as $index => $detailData) {
                // Cari detail pengajuan berdasarkan form_field_id
                $detailPengajuan = $pengajuan->detailPengajuan()
                    ->where('form_field_id', $detailData['form_field_id'])
                    ->first();
            
                if (!$detailPengajuan) {
                    continue;
                }
            
                $filePath = null;
                
                // PERBAIKAN: Cek file dengan cara yang benar
                $fileKey = "details.{$index}.file_bukti";
                if ($request->hasFile($fileKey)) {
                    $file = $request->file($fileKey);
                    
                    // Buat path yang aman
                    $kategoriDetail = $detailPengajuan->formField->label ?? 'uncategorized';
                    $kategoriDetail = preg_replace('/[^a-zA-Z0-9_-]/', '_', $kategoriDetail);
                    
                    $fileName = time() . '_' . $index . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                    $detailPath = "assets/karyawan/settlement/{$kategoriPengajuan}/{$namaKaryawan}/{$kategoriDetail}";
                    
                    // Simpan file
                    $filePath = $file->storeAs($detailPath, $fileName, 'custom_public');
                    
                    // Log untuk debugging
                    \Log::info("File bukti detail disimpan", [
                        'index' => $index,
                        'original_name' => $file->getClientOriginalName(),
                        'stored_path' => $filePath,
                        'file_size' => $file->getSize()
                    ]);
                }
            
                $actualAmount = (float) $detailData['actual_amount'];
            
                DetailSettlement::create([
                    'settlement_id' => $settlement->id,
                    'form_field_id' => $detailData['form_field_id'],
                    'detail_pengajuan_id' => $detailPengajuan->id,
                    'keterangan' => $detailData['label'],
                    'nominal' => $actualAmount,
                    'file_bukti' => $filePath,
                    'catatan' => $detailData['catatan'] ?? null,
                    'tanggal_transaksi' => now()
                ]);
            }
    
            // Update total di settlement dengan nilai yang benar
            $settlement->update([
                'total_actual' => $totalActual,
                'selisih' => $selisih // Menggunakan selisih yang sudah dihitung dengan benar
            ]);
    
            // Update pengajuan
            $pengajuan->markSettlementCreated($settlement->id);
    
            DB::commit();

            \App\Helpers\ActivityLogger::log('Create Settlement', "User created settlement {$settlement->nomor_settlement} for pengajuan {$pengajuan->nomor_pengajuan}");
    
            return response()->json([
                'success' => true,
                'message' => 'Settlement berhasil disimpan',
                'redirect' => route('settlement.show', $settlement->id)
            ]);
    
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Gagal menyimpan settlement: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function show($id)
    {
        try {
            $settlement = Settlement::with([
                'pengajuan' => function($query) {
                    $query->with(['requester', 'kategoriPengajuan', 'detailPengajuan.formField']);
                },
                'details' => function($query) {
                    $query->orderBy('created_at');
                }
            ])->findOrFail($id);
            
            // Validasi ownership
            if ($settlement->pengajuan->requester_id != Auth::id()) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melihat settlement ini.');
            }
    
            // Data perbandingan original vs actual
            $comparison = $this->getComparisonData($settlement);
    
            return view('Approval-app.Karyawan.Settlement.show', compact('settlement', 'comparison'));
            
        } catch (\Exception $e) {
            return redirect()->route('settlement.index')
                ->with('error', 'Settlement tidak ditemukan.');
        }
    }
    
    public function sendRefundNotification(Request $request, $id)
    {
        try {
            $settlement = Settlement::with(['pengajuan.requester'])
                ->findOrFail($id);
            
            // Validasi bahwa status_realisasi adalah under
            if ($settlement->status_realisasi !== 'under') {
                return response()->json(['error' => 'Settlement tidak memerlukan pengembalian'], 400);
            }
            
            $requester = $settlement->pengajuan->requester;
            $message = $request->input('message', '');
            
            // Kirim email notifikasi
            Mail::to($requester->email)->send(new SettlementRefundNotification($settlement, $message));
            
            // Log notifikasi
            EmailNotificationLog::create([
                'settlement_id' => $settlement->id,
                'pengajuan_id' => $settlement->pengajuan_id,
                'recipient_id' => $requester->id,
                'recipient_email' => $requester->email,
                'email_type' => 'refund_notification',
                'status' => 'success',
                'message' => 'Notifikasi pengembalian dana settlement',
                'sent_at' => now()
            ]);
            
            // Update settlement untuk menandai bahwa notifikasi sudah dikirim
            $settlement->update([
                'catatan_settlement' => $settlement->catatan_settlement . "\n[" . now()->format('d/m/Y H:i') . "] Notifikasi pengembalian dikirim oleh Finance"
            ]);
            
            return response()->json(['success' => 'Notifikasi berhasil dikirim']);
    
        } catch (\Exception $e) {
            Log::error('Error sending refund notification: ' . $e->getMessage());
    
            // Log error notifikasi jika settlement ada
            if (isset($settlement) && isset($requester)) {
                $this->logNotification($settlement, $requester, 'failed', $e->getMessage());
            }
    
            return response()->json(['error' => 'Gagal mengirim notifikasi'], 500);
        }
    }
    
    public function showDetail($id)
    {
        try {
            $settlement = Settlement::with([
                'pengajuan.requester',
                'pengajuan.kategoriPengajuan',
                'details.formField'
            ])->findOrFail($id);
    
            // Generate HTML content untuk modal
            $html = view('Approval-app.Karyawan.Settlement.detail-modal', compact('settlement'))->render();
    
            return response()->json([
                'html' => $html,
                'settlement' => $settlement
            ]);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'Settlement tidak ditemukan'], 404);
        }
    }
    
    private function generateNomorSettlement($pengajuan)
    {
        $tahun = date('Y');
        $bulan = date('m');
        
        $lastNumber = Settlement::whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->count();
        
        $sequence = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        
        return 'STL/' . $tahun . $bulan . '/' . $sequence;
    }

    public function edit($id)
    {
        $settlement = Settlement::with(['pengajuan.kategoriPengajuan', 'pengajuan.detailPengajuan.formField', 'details'])->findOrFail($id);
        
        // Validasi ownership
        if ($settlement->pengajuan->requester_id != Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit settlement ini.');
        }

        // Validasi status settlement (hanya bisa edit jika status tertentu)
        if (!in_array($settlement->status_settlement, ['draft', 'pending', 'submitted', 'revision'])) {
            return redirect()->back()->with('error', 'Settlement tidak dapat diedit karena sudah diproses.');
        }

        return view('Approval-app.Karyawan.Settlement.edit', compact('settlement'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'details' => 'required|array|min:1',
            'details.*.form_field_id' => 'required|integer|exists:FormField,id',
            'details.*.detail_pengajuan_id' => 'nullable|integer|exists:DetailPengajuan,id',
            'details.*.keterangan' => 'required|string|max:255',
            'details.*.tanggal_transaksi' => 'required|date|before_or_equal:today',
            'details.*.nominal' => 'required|numeric|min:0.01',
            'details.*.kategori_biaya' => 'required|string|max:100',
            'details.*.file_bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'details.*.catatan' => 'nullable|string|max:500',
            'catatan_settlement' => 'nullable|string|max:1000'
        ], [
            'details.required' => 'Minimal harus ada satu item settlement',
            'details.*.keterangan.required' => 'Keterangan harus diisi',
            'details.*.tanggal_transaksi.required' => 'Tanggal transaksi harus diisi',
            'details.*.tanggal_transaksi.before_or_equal' => 'Tanggal transaksi tidak boleh melebihi hari ini',
            'details.*.nominal.required' => 'Nominal harus diisi',
            'details.*.nominal.min' => 'Nominal harus lebih dari 0',
            'details.*.kategori_biaya.required' => 'Kategori biaya harus dipilih',
            'details.*.file_bukti.mimes' => 'File bukti harus berformat PDF, JPG, JPEG, atau PNG',
            'details.*.file_bukti.max' => 'Ukuran file bukti maksimal 5MB'
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

            $settlement = Settlement::with(['pengajuan', 'details'])->findOrFail($id);
            
            // Validasi ownership
            if ($settlement->pengajuan->requester_id != Auth::id()) {
                throw new \Exception('Anda tidak memiliki akses untuk mengedit settlement ini.');
            }

            // Validasi status settlement
            if (!in_array($settlement->status_settlement, ['draft', 'pending', 'submitted', 'revision'])) {
                throw new \Exception('Settlement tidak dapat diedit karena sudah diproses.');
            }

            // Hitung total actual baru
            $totalActual = 0;
            foreach ($request->details as $detail) {
                $totalActual += $detail['nominal'];
            }

            $selisih = $settlement->pengajuan->nominal_pengajuan - $totalActual;

            // Update settlement
            $settlement->update([
                'total_actual' => $totalActual,
                'selisih' => $selisih,
                'status_settlement' => 'draft',
                'catatan_settlement' => $request->catatan_settlement,
                'tanggal_settlement' => now()
            ]);

            // Hapus detail lama beserta file
            foreach ($settlement->details as $oldDetail) {
                if ($oldDetail->file_bukti) {
                    Storage::disk('custom_public')->delete($oldDetail->file_bukti);
                }
            }
            $settlement->details()->delete();

            // Simpan detail settlement baru
            foreach ($request->details as $index => $detail) {
                $filePath = null;
                
                // Upload file bukti jika ada
                if (isset($detail['file_bukti']) && $detail['file_bukti']) {
                    $file = $detail['file_bukti'];
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $filename = $originalName . '_' . time() . '_' . $index . '.' . $extension;
                    $filePath = $file->storeAs('settlement/bukti', $filename, 'custom_public');
                }

                DetailSettlement::create([
                    'settlement_id' => $settlement->id,
                    // ✅ PASTIKAN FIELD INI ADA DI REQUEST
                    'form_field_id' => $detail['form_field_id'], 
                    'detail_pengajuan_id' => $detail['detail_pengajuan_id'] ?? null, // Tambahkan ini jika dibutuhkan
                    // ------------------------------------
                    'keterangan' => $detail['keterangan'],
                    'tanggal_transaksi' => $detail['tanggal_transaksi'],
                    'nominal' => $detail['nominal'],
                    'kategori_biaya' => $detail['kategori_biaya'],
                    'file_bukti' => $filePath,
                    'catatan' => $detail['catatan'] ?? null
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Settlement berhasil diupdate',
                'redirect' => route('settlement.index')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDetailSettlement($id)
{
    try {
        $settlement = Settlement::with([
            'details' => function($query) {
                $query->orderBy('created_at');
            },
            'pengajuan' => function($query) {
                $query->with(['requester', 'kategoriPengajuan', 'detailPengajuan.formField']);
            }
        ])->findOrFail($id);
        
        // ✅ PERUBAHAN UTAMA: Dapatkan data kalkulasi Nominal Awal (Original Values)
        $calculatedOriginals = $this->calculateOriginalValues($settlement->pengajuan);
        $originalMap = collect($calculatedOriginals['details'])->keyBy('detail_id');
        
        // Ambil progress approval untuk settlement ini
        $progressApprovals = ProgressApproval::with(['approver', 'flowApproval'])
            ->where('settlement_id', $id)
            ->orderBy('urutan')
            ->get();

        // Transformasi data progress approval untuk timeline horizontal
        $timelineData = [];
        foreach ($progressApprovals as $progress) {
            $timelineData[] = [
                'urutan' => $progress->urutan,
                'step_name' => $progress->step_name,
                'approver_name' => $progress->approver ? $progress->approver->nama : 'Belum ditentukan',
                'approver_jabatan' => $progress->approver ? $progress->approver->jabatan : 'Belum ditentukan',
                'approver_email' => $progress->approver ? $progress->approver->email : null,
                'status' => $progress->status,
                'tanggal_approval' => $progress->tanggal_approval,
                'catatan' => $progress->catatan,
                'step_type' => $progress->step_type,
                'is_current' => ($progress->status == 'pending' && 
                                !is_null($progress->approver_id) && // Cek approver tidak null
                                !$progressApprovals->where('urutan', '<', $progress->urutan)->whereIn('status', ['pending', 'proses'])->count()),
                'is_completed' => in_array($progress->status, ['approved', 'completed']),
                'is_rejected' => ($progress->status == 'rejected'),
                'is_pending' => ($progress->status == 'pending')
            ];
        }

        // Format data untuk response
        $formattedSettlement = [
            'id' => $settlement->id,
            'nomor_settlement' => $settlement->nomor_settlement,
            'tanggal_settlement' => $settlement->tanggal_settlement,
            'total_actual' => $settlement->total_actual,
            'selisih' => $settlement->selisih,
            'status_settlement' => $settlement->status_settlement,
            'catatan_settlement' => $settlement->catatan_settlement,
            'file_bukti_transfer' => $settlement->file_bukti_transfer,
            'tanggal_transfer' => $settlement->tanggal_transfer,
            'status_realisasi' => $settlement->status_realisasi,
            'timeline_data' => $timelineData, // Tambahan data timeline
            'pengajuan' => [
                'nomor_pengajuan' => $settlement->pengajuan->nomor_pengajuan,
                'judul' => $settlement->pengajuan->judul,
                'nominal_pengajuan' => $settlement->pengajuan->nominal_pengajuan,
                'mata_uang' => $settlement->pengajuan->mata_uang,
                'requester' => [
                    'nama' => $settlement->pengajuan->requester->nama ?? 'N/A'
                ],
                'kategoriPengajuan' => [
                    'nama' => $settlement->pengajuan->kategoriPengajuan->nama ?? 'N/A'
                ]
            ],
            // ✅ MAPPING ULANG DETAILS
            'details' => $settlement->details->map(function($detail) use ($originalMap) {
                // Dapatkan data kalkulasi dari map menggunakan detail_pengajuan_id
                $originalCalculated = $originalMap->get($detail->detail_pengajuan_id);
                
                return [
                    'id' => $detail->id,
                    'keterangan' => $detail->keterangan,
                    'tanggal_transaksi' => $detail->tanggal_transaksi,
                    'nominal' => $detail->nominal,
                    // ✅ MENGAMBIL NILAI YANG SUDAH DIKALIKAN DARI calculateOriginalValues
                    'original_nominal' => $originalCalculated ? $originalCalculated['calculated_value'] : 0, 
                    // -----------------------------------------------------------
                    'kategori_biaya' => $detail->kategori_biaya,
                    'file_bukti' => $detail->file_bukti,
                    'catatan' => $detail->catatan
                ];
            })
        ];

        return response()->json([
            'success' => true,
            'data' => $formattedSettlement
        ]);
    } catch (\Exception $e) {
        \Log::error('Error getDetailSettlement: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Data settlement tidak ditemukan. Error: ' . $e->getMessage()
        ], 404);
    }
}
    
    private function getComparisonData($settlement)
    {
        $comparison = [];
        $originalDetails = $settlement->pengajuan->detailPengajuan->where('formField.type', 'currency');
        
        foreach ($settlement->details as $index => $detail) {
            $originalDetail = $originalDetails->skip($index)->first();
            if ($originalDetail) {
                $comparison[] = [
                    'item' => $detail->keterangan,
                    'original' => $originalDetail->nilai,
                    'actual' => $detail->nominal,
                    'selisih' => $originalDetail->nilai - $detail->nominal,
                    'file_bukti' => $detail->file_bukti
                ];
            }
        }
        
        return $comparison;
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $settlement = Settlement::with(['pengajuan', 'details'])->findOrFail($id);
            
            // Validasi ownership
            if ($settlement->pengajuan->requester_id != Auth::id()) {
                throw new \Exception('Anda tidak memiliki akses untuk menghapus settlement ini.');
            }

            // Validasi status settlement
            if (!in_array($settlement->status_settlement, ['draft', 'pending'])) {
                throw new \Exception('Settlement tidak dapat dihapus karena sudah diproses.');
            }

            // Hapus file bukti
            foreach ($settlement->details as $detail) {
                if ($detail->file_bukti) {
                    Storage::disk('custom_public')->delete($detail->file_bukti);
                }
            }

            // Hapus detail settlement
            $settlement->details()->delete();

            // Update pengajuan
            $settlement->pengajuan->update([
                'settlement_id' => null,
                'status_pengajuan' => 'approved'
            ]);

            // Hapus settlement
            $settlement->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Settlement berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadFile($settlementId, $detailId)
    {
        try {
            $detail = DetailSettlement::where('settlement_id', $settlementId)
                ->where('id', $detailId)
                ->firstOrFail();

            $settlement = Settlement::with('pengajuan')->findOrFail($settlementId);
            
            // Validasi ownership
            if ($settlement->pengajuan->requester_id != Auth::id()) {
                abort(403, 'Anda tidak memiliki akses untuk mengunduh file ini.');
            }

            if (!$detail->file_bukti || !Storage::disk('custom_public')->exists($detail->file_bukti)) {
                abort(404, 'File tidak ditemukan.');
            }

            return Storage::disk('custom_public')->download($detail->file_bukti);

        } catch (\Exception $e) {
            abort(404, 'File tidak ditemukan atau tidak dapat diakses.');
        }
    }
}