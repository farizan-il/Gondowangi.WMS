<?php

namespace App\Http\Controllers\Employments;

use App\Http\Controllers\Controller;
use App\Models\KategoriPengajuan;
use App\Models\FormField;
use App\Models\Pengajuan;
use App\Models\DetailPengajuan;
use App\Models\HistoryPengajuan;
use App\Models\Department;
use App\Models\TransactionRequest;
use App\Models\RoleLevel;
use App\Models\Karyawan;
use App\Models\FlowApproval;
use App\Models\ProgressApproval;
use App\Models\EmailNotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\PengajuanBaruNotification;
use App\Jobs\SendPengajuanEmailNotification;
use Illuminate\Support\Facades\Storage;

// controller untuk membuat pengajuan
class PengajuanController extends Controller
{
    public function index()
    {
        // Ambil data kategori pengajuan
        $kategoriPengajuan = KategoriPengajuan::aktif()
            ->orderBy('nama')
            ->get();
    
        // Ambil data pengajuan milik user yang sedang login DAN belum completed
        $pengajuanList = Pengajuan::with([
            'kategoriPengajuan',
            'requester',
            'settlement',
            'transactionRequest',
            'emailNotificationLogs',
            'historyPengajuan' => function($query) {
                $query->latest('created_at');
            }
        ])
        ->where('requester_id', Auth::id())
        ->where('status_pengajuan', '!=', 'completed') // Tambahan filter status
        ->orderBy('created_at', 'desc')
        ->get();
        
        // Hitung data belum completed
        $pendingPengajuan = \App\Models\Pengajuan::where('status_pengajuan', '!=', 'completed')->count();
        $pendingSettlement = \App\Models\Settlement::where('status_settlement', '!=', 'approved')->count();
        
        $totalPending = $pendingPengajuan + $pendingSettlement;
    
        return view('Approval-app.Karyawan.Pengajuan.index', 
            compact('kategoriPengajuan', 'pengajuanList', 'totalPending', 'pendingPengajuan', 'pendingSettlement'));
    }
    
    public function getEditForm($id)
    {
        $pengajuan = Pengajuan::with(['detailPengajuan.formField', 'kategoriPengajuan'])->findOrFail($id);

        if ($pengajuan->requester_id !== Auth::id() || $pengajuan->status_pengajuan !== 'revision') {
            return '<div class="alert alert-danger">Anda tidak memiliki izin untuk merevisi pengajuan ini.</div>';
        }

        // 1. Mapping Data Mentah dari Database
        $currentData = [];
        foreach ($pengajuan->detailPengajuan as $detail) {
            if ($detail->formField) {
                $currentData[$detail->formField->nama_field] = $detail->nilai;
            }
        }

        // 2. LOGIKA FIX TANGGAL: Pecah Range String menjadi Dari & Sampai
        // Loop untuk Perjalanan 1, 2, dan 3
        for ($i = 1; $i <= 3; $i++) {
            $keyDari   = "perjalanan{$i}_tanggal_dari";
            $keySampai = "perjalanan{$i}_tanggal_sampai";
            
            // Cek variasi nama field yang mungkin menyimpan range gabungan
            // Sesuaikan dengan nama field di table FormField database Anda
            $possibleRangeKeys = [
                "perjalanan{$i}_tanggal",      // Kemungkinan 1
                "tanggal_perjalanan_{$i}",     // Kemungkinan 2
                "tgl_perjalanan_{$i}",         // Kemungkinan 3
                "perjalanan_{$i}_tanggal"      // Kemungkinan 4
            ];

            $foundRange = null;
            foreach($possibleRangeKeys as $key) {
                if (!empty($currentData[$key])) {
                    $foundRange = $currentData[$key];
                    break;
                }
            }

            // Jika ditemukan data range (contoh: "2025-11-01 - 2025-11-05")
            if ($foundRange && strpos($foundRange, ' - ') !== false) {
                $dates = explode(' - ', $foundRange);
                if (count($dates) == 2) {
                    // Masukkan ke array $currentData dengan key yang diharapkan oleh Form HTML
                    // Pastikan format Y-m-d untuk input type="date"
                    $currentData[$keyDari]   = date('Y-m-d', strtotime(trim($dates[0])));
                    $currentData[$keySampai] = date('Y-m-d', strtotime(trim($dates[1])));
                }
            } 
            // Jika data tersimpan sudah terpisah tapi formatnya mungkin salah (misal d/m/Y), kita normalkan
            else {
                if (!empty($currentData[$keyDari])) {
                    $currentData[$keyDari] = date('Y-m-d', strtotime($currentData[$keyDari]));
                }
                if (!empty($currentData[$keySampai])) {
                    $currentData[$keySampai] = date('Y-m-d', strtotime($currentData[$keySampai]));
                }
            }
        }
        
        // Ambil struktur form fields
        $formFields = FormField::where('kategori_pengajuan_id', $pengajuan->kategori_pengajuan_id)
            ->orderBy('urutan')
            ->get();

        return view('Approval-app.Karyawan.Pengajuan.partials.form_revisi', compact('pengajuan', 'currentData', 'formFields'));
    }

    public function updateRevisi(Request $request, $id)
    {
        $pengajuan = Pengajuan::where('requester_id', Auth::id())->findOrFail($id);

        DB::beginTransaction();
        try {
            // 1. Update Data Utama (Header)
            // Hitung ulang total nominal pengajuan secara keseluruhan
            $nominalPengajuan = $this->calculateNominalFromCurrencyFields($request);
            
            $pengajuan->update([
                'judul' => $request->judul ?? $pengajuan->judul,
                'deskripsi' => $request->deskripsi ?? $pengajuan->deskripsi,
                'nominal_pengajuan' => $nominalPengajuan,
                'catatan_requester' => $request->catatan_requester,
                'status_pengajuan' => 'proses' // Kembalikan status ke proses
            ]);

            // 2. Update Detail Fields (DetailPengajuan)
            if ($request->has('form_data')) {
                foreach ($request->form_data as $fieldName => $value) {
                    // Cari FormField untuk mendapatkan ID-nya
                    $formField = FormField::where('kategori_pengajuan_id', $pengajuan->kategori_pengajuan_id)
                        ->where('nama_field', $fieldName)
                        ->first();

                    if ($formField) {
                        // Inisialisasi jumlah hari default null (untuk field biasa)
                        $jumlahHari = null;

                        // --- LOGIKA KHUSUS: Hitung Ulang Hari/Malam untuk Hotel & Makan ---
                        
                        // A. Cek jika field adalah HOTEL (Hitung Malam)
                        if (strpos($fieldName, 'hotel_biaya') !== false) {
                            // 1. Tentukan ini perjalanan ke berapa (1, 2, atau 3)
                            $tripNum = '1'; 
                            if (preg_match('/_(\d+)$/', $fieldName, $matches)) {
                                $tripNum = $matches[1]; // Ambil angka di belakang (misal hotel_biaya_2)
                            }

                            // 2. Ambil tanggal dari request form_data
                            $tglDari = $request->input("form_data.perjalanan{$tripNum}_tanggal_dari") 
                                    ?? $request->input("form_data.tanggal_perjalanan_{$tripNum}"); // Fallback naming
                            $tglSampai = $request->input("form_data.perjalanan{$tripNum}_tanggal_sampai");

                            // 3. Hitung selisih malam
                            if ($tglDari && $tglSampai) {
                                $d1 = new \DateTime($tglDari);
                                $d2 = new \DateTime($tglSampai);
                                if ($d2 > $d1) {
                                    $interval = $d1->diff($d2);
                                    $jumlahHari = $interval->days; // Malam = selisih hari murni
                                } else {
                                    $jumlahHari = 0;
                                }
                            }
                        }

                        // B. Cek jika field adalah MAKAN (Hitung Hari)
                        elseif (strpos($fieldName, 'makan_biaya') !== false) {
                            // 1. Tentukan trip number
                            $tripNum = '1'; 
                            if (preg_match('/_(\d+)$/', $fieldName, $matches)) {
                                $tripNum = $matches[1];
                            }

                            // 2. Ambil tanggal
                            $tglDari = $request->input("form_data.perjalanan{$tripNum}_tanggal_dari");
                            $tglSampai = $request->input("form_data.perjalanan{$tripNum}_tanggal_sampai");

                            // 3. Hitung selisih hari (+1 karena hari pertama dihitung)
                            if ($tglDari && $tglSampai) {
                                $d1 = new \DateTime($tglDari);
                                $d2 = new \DateTime($tglSampai);
                                if ($d2 >= $d1) {
                                    $interval = $d1->diff($d2);
                                    $jumlahHari = $interval->days + 1; // Hari = selisih + 1
                                } else {
                                    $jumlahHari = 0;
                                }
                            }
                        }
                        
                        // C. Cek jika field adalah TANGGAL (Update tanggalnya juga)
                        elseif (strpos($fieldName, '_tanggal_dari') !== false) {
                            // Jika field ini adalah tanggal dari, kita cari pasangannya untuk hitung durasi global (opsional)
                            // Tapi biasanya tanggal disimpan sebagai value string saja di DetailPengajuan
                            $jumlahHari = 1; 
                        }

                        // --- AKHIR LOGIKA KHUSUS ---

                        // Update atau Create Data di Database
                        // Kita gunakan updateOrCreate untuk memastikan data tersimpan
                        $dataToUpdate = [
                            'nilai' => is_array($value) ? json_encode($value) : $value
                        ];

                        // Hanya update kolom jumlah_hari jika hasil perhitungan tidak null
                        // Ini mencegah field biasa (seperti transportasi) tertimpa jadi null/0
                        if (!is_null($jumlahHari)) {
                            $dataToUpdate['jumlah_hari'] = $jumlahHari;
                        }

                        DetailPengajuan::updateOrCreate(
                            [
                                'pengajuan_id' => $pengajuan->id, 
                                'form_field_id' => $formField->id
                            ],
                            $dataToUpdate
                        );
                    }
                }
            }

            // 3. Update File Pendukung (Append)
            if ($request->hasFile('file_pendukung')) {
                $newFiles = [];
                foreach ($request->file('file_pendukung') as $file) {
                    $newFiles[] = $file->store('pengajuan/files', 'custom_public');
                }
                $existingFiles = $pengajuan->file_pendukung ?? [];
                $pengajuan->file_pendukung = array_merge($existingFiles, $newFiles);
                $pengajuan->save();
            }

            // 4. Reset Status Approval pada Step Terkait
            $currentStep = $pengajuan->current_step;
            $progressApproval = ProgressApproval::where('pengajuan_id', $pengajuan->id)
                ->where('urutan', $currentStep)
                ->first();

            if ($progressApproval) {
                $progressApproval->update([
                    'status' => 'proses',
                    'tanggal_approval' => null,
                    'catatan' => null // Bersihkan catatan revisi lama
                ]);
            }

            // 5. Catat History
            HistoryPengajuan::createHistory(
                $pengajuan->id,
                'revised', 
                'revision',
                'proses',
                Auth::id(),
                'Pengajuan direvisi (Nominal: Rp ' . number_format($nominalPengajuan, 0, ',', '.') . ')',
                $request->catatan_requester,
                $progressApproval ? $progressApproval->step_name : 'Revisi',
                $currentStep
            );

            DB::commit();

            \App\Helpers\ActivityLogger::log('Revise Pengajuan', "User revised pengajuan {$pengajuan->nomor_pengajuan}");
            // Opsional: Kirim notifikasi
            // $this->sendEmailWithMonitoring($pengajuan);

            return response()->json(['success' => true, 'message' => 'Revisi berhasil disimpan dan dikirim kembali.']);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Update Revisi Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    
    public function PengajuanSelesai()
    {
        // Ambil data kategori pengajuan
        $kategoriPengajuan = KategoriPengajuan::aktif()
            ->orderBy('nama')
            ->get();
    
        // Ambil data pengajuan milik user yang sedang login DAN sudah completed
        $pengajuanList = Pengajuan::with([
            'kategoriPengajuan',
            'requester',
            'settlement',
            'transactionRequest',
            'emailNotificationLogs',
            'historyPengajuan' => function($query) {
                $query->latest('created_at');
            }
        ])
        ->where('requester_id', Auth::id())
        ->where('status_pengajuan', 'completed') // Tambahan filter status
        ->orderBy('created_at', 'desc')
        ->get();
        
        // Hitung data belum completed
        $pendingPengajuan = \App\Models\Pengajuan::where('status_pengajuan', '!=', 'completed')->count();
        $pendingSettlement = \App\Models\Settlement::where('status_settlement', '!=', 'approved')->count();
        
        $totalPending = $pendingPengajuan + $pendingSettlement;
    
        return view('Approval-app.Karyawan.Pengajuan.PengajuanSelesai', 
            compact('kategoriPengajuan', 'pengajuanList', 'totalPending', 'pendingPengajuan', 'pendingSettlement'));
    }

    public function printPdf($id)
    {   
        try {
            $pengajuan = Pengajuan::with([
                'kategoriPengajuan',
                'requester',
                'settlement',
                'historyPengajuan',
                'detailPengajuan.formField'
            ])->find($id);
        
            if (!$pengajuan) {
                return redirect()->back()->with('error', 'Data pengajuan tidak ditemukan');
            }
        
            // Query untuk mengambil progress approval dengan settlement_id = null
            $progressApprovals = ProgressApproval::with(['approver', 'flowApproval'])
                ->where('pengajuan_id', $id)
                ->whereNull('settlement_id')
                ->orderBy('urutan')
                ->get();
        
            // Transformasi data detail pengajuan dengan validasi yang lebih baik
            $detailFields = [];
            $fieldData = [];
            
            foreach ($pengajuan->detailPengajuan as $detail) {
                // Skip jika formField tidak ada
                if (!$detail->formField) {
                    \Log::warning('FormField tidak ditemukan untuk detail ID: ' . $detail->id);
                    continue;
                }
                
                $fieldInfo = [
                    'name' => $detail->formField->nama_field,
                    'label' => $detail->formField->label,
                    'type' => $detail->formField->tipe_field,
                    'value' => $detail->nilai ?? '',
                    'jumlah_hari' => $detail->jumlah_hari ?? 0,
                    'urutan' => $detail->formField->urutan ?? 999
                ];
                
                $detailFields[] = $fieldInfo;
                $fieldData[$fieldInfo['name']] = $fieldInfo['value'];
            }
        
            // Sort berdasarkan urutan field
            usort($detailFields, function($a, $b) {
                return $a['urutan'] <=> $b['urutan'];
            });
            
            // Hitung jumlah hari per perjalanan berdasarkan tanggal
            $hariPerPerjalanan = [1, 1, 1]; // Default 1 hari jika tidak bisa dihitung
            
            // Function untuk menghitung selisih hari dari rentang tanggal
            $hitungHari = function($tanggalRange) {
                if (!$tanggalRange || $tanggalRange === '-') return 1;
                
                // Coba parse rentang tanggal (format: 2025-09-12 - 2025-09-19)
                if (strpos($tanggalRange, ' - ') !== false) {
                    $dates = explode(' - ', $tanggalRange);
                    if (count($dates) == 2) {
                        try {
                            $startDate = \Carbon\Carbon::parse(trim($dates[0]));
                            $endDate = \Carbon\Carbon::parse(trim($dates[1]));
                            $diffInDays = $startDate->diffInDays($endDate) + 1; // +1 karena termasuk hari pertama
                            return max(1, $diffInDays); // minimal 1 hari
                        } catch (\Exception $e) {
                            \Log::warning("Error parsing date range: {$tanggalRange}");
                        }
                    }
                }
                
                return 1; // default 1 hari
            };
            
            // Hitung hari untuk setiap perjalanan
            for ($i = 1; $i <= 3; $i++) {
                $tanggalKeys = [
                    "perjalanan{$i}_tanggal",
                    "tanggal_perjalanan_{$i}",
                    "tgl_perjalanan_{$i}",
                    "perjalanan_{$i}_tanggal"
                ];
                
                $tanggalRange = '';
                foreach ($tanggalKeys as $key) {
                    if (isset($fieldData[$key]) && $fieldData[$key] !== '') {
                        $tanggalRange = $fieldData[$key];
                        break;
                    }
                }
                
                $hariPerPerjalanan[$i-1] = $hitungHari($tanggalRange);
                \Log::info("Perjalanan {$i}: {$tanggalRange} = {$hariPerPerjalanan[$i-1]} hari");
            }
            
            // Jika tidak ada tanggal yang valid, coba cari dari field jumlah hari global
            $globalHari = 0;
            foreach ($pengajuan->detailPengajuan as $detail) {
                if ($detail->formField) {
                    $fieldName = strtolower($detail->formField->nama_field ?? '');
                    
                    if (strpos($fieldName, 'jumlah_hari') !== false || 
                        strpos($fieldName, 'hari_hotel') !== false ||
                        strpos($fieldName, 'hari_perjalanan') !== false) {
                        $globalHari = (int)($detail->nilai ?? $detail->jumlah_hari ?? 0);
                        break;
                    }
                    
                    if ($detail->jumlah_hari > 0) {
                        $globalHari = $detail->jumlah_hari;
                    }
                }
            }
            
            // Jika ada field jumlah hari global dan tidak ada tanggal yang valid, gunakan global
            if ($globalHari > 0 && array_sum($hariPerPerjalanan) == 3) { // 3 = default (1+1+1)
                $hariPerPerjalanan = [$globalHari, $globalHari, $globalHari];
            }
        
            // Hitung data biaya perjalanan dengan hari per perjalanan yang berbeda
            $biayaPerjalananData = $this->calculateBiayaPerjalanan($fieldData, $hariPerPerjalanan);
            
        
            // Transformasi progress approval untuk timeline
            $progressData = [];
            foreach ($progressApprovals as $progress) {
                $progressData[] = [
                    'urutan' => $progress->urutan,
                    'step_name' => $progress->step_name ?? 'Step ' . $progress->urutan,
                    'approver_name' => $progress->approver ? $progress->approver->nama : 'Belum ditentukan',
                    'approver_jabatan' => $progress->approver ? $progress->approver->jabatan : 'Belum ditentukan',
                    'approver_email' => $progress->approver ? $progress->approver->email : null,
                    'status' => $progress->status,
                    'tanggal_approval' => $progress->tanggal_approval,
                    'catatan' => $progress->catatan ?? '-',
                    'is_current' => ($progress->urutan == $pengajuan->current_step),
                    'is_completed' => in_array($progress->status, ['approved', 'completed']),
                    'is_rejected' => ($progress->status == 'rejected')
                ];
            }
        
            // Assign data ke pengajuan object
            $pengajuan->detail_fields = $detailFields;
            $pengajuan->field_data = $fieldData;
            $pengajuan->progressApprovals = collect($progressData);
            $pengajuan->biaya_perjalanan_data = $biayaPerjalananData;
            
            return view('Approval-app.Karyawan.Pengajuan.print-pdf', compact('pengajuan'));
            
        } catch (\Exception $e) {
            \Log::error('Error generating PDF: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat PDF: ' . $e->getMessage());
        }
    }
    
    private function calculateBiayaPerjalanan($fieldData, $hariPerPerjalanan = [1, 1, 1])
    {
        // Helper function untuk konversi nilai ke float
        $toFloat = function($value) {
            if (!$value || $value === '' || $value === '0') return 0;
            
            // Jika sudah numeric
            if (is_numeric($value)) {
                return floatval($value);
            }
            
            // Jika string, bersihkan dari karakter non-numeric kecuali titik dan minus
            $cleaned = preg_replace('/[^\d.-]/', '', $value);
            $numValue = floatval($cleaned);
            
            return is_nan($numValue) ? 0 : $numValue;
        };
    
        // Inisialisasi array dengan nilai default 0
        $transportasiDarat = [0, 0, 0];
        $transportasiTaxi = [0, 0, 0];
        $transportasiTaxi = [0, 0, 0];
        $hotelBiaya = [0, 0, 0];
        $makanBiaya = [0, 0, 0];
        $uangSaku = [0, 0, 0];
        $telephoneFax = [0, 0, 0];
        $entertainment = [0, 0, 0];
        $dokumentasi = [0, 0, 0];
        $lainLain = [0, 0, 0];
    
        // Mapping field name patterns - sesuaikan dengan nama field di database
        $fieldMappings = [
            // Transportasi Darat
            'transportasi_darat' => ['transportasi_darat', 'transport_darat', 'darat'],
            'transportasi_darat_2' => ['transportasi_darat_2', 'transport_darat_2', 'darat_2'],
            'transportasi_darat_3' => ['transportasi_darat_3', 'transport_darat_3', 'darat_3'],
            
            'transportasi_udara_1' => ['transportasi_udara_1', 'transport_udara_1', 'pesawat_1'],
            'transportasi_udara_2' => ['transportasi_udara_2', 'transport_udara_2', 'pesawat_2'],
            'transportasi_udara_3' => ['transportasi_udara_3', 'transportasi_udara_3', 'pesawat_3'],
            
            // Transportasi Taxi/Airport
            'transportasi_taxi' => ['transportasi_taxi', 'transport_taxi', 'taxi', 'airport_tax'],
            'transportasi_taxi_2' => ['transportasi_taxi_2', 'transport_taxi_2', 'taxi_2', 'airport_tax_2'],
            'transportasi_taxi_3' => ['transportasi_taxi_3', 'transport_taxi_3', 'taxi_3', 'airport_tax_3'],
            
            // Hotel
            'hotel_biaya' => ['hotel_biaya', 'hotel', 'biaya_hotel'],
            'hotel_biaya_2' => ['hotel_biaya_2', 'hotel_2', 'biaya_hotel_2'],
            'hotel_biaya_3' => ['hotel_biaya_3', 'hotel_3', 'biaya_hotel_3'],
            
            // Makan
            'makan_biaya' => ['makan_biaya', 'makan', 'biaya_makan'],
            'makan_biaya_2' => ['makan_biaya_2', 'makan_2', 'biaya_makan_2'],
            'makan_biaya_3' => ['makan_biaya_3', 'makan_3', 'biaya_makan_3'],
            
            // Uang Saku
            'uang_saku' => ['uang_saku', 'saku'],
            'uang_saku_2' => ['uang_saku_2', 'saku_2'],
            'uang_saku_3' => ['uang_saku_3', 'saku_3'],
            
            // Telephone & Fax
            'telephone_fax' => ['telephone_fax', 'telp_fax', 'phone_fax'],
            'telephone_fax_2' => ['telephone_fax_2', 'telp_fax_2', 'phone_fax_2'],
            'telephone_fax_3' => ['telephone_fax_3', 'telp_fax_3', 'phone_fax_3'],
            
            // Entertainment
            'entertainment' => ['entertainment', 'hiburan'],
            'entertainment_2' => ['entertainment_2', 'hiburan_2'],
            'entertainment_3' => ['entertainment_3', 'hiburan_3'],
            
            // Dokumentasi
            'dokumentasi' => ['dokumentasi', 'dokumen'],
            'dokumentasi_2' => ['dokumentasi_2', 'dokumen_2'],
            'dokumentasi_3' => ['dokumentasi_3', 'dokumen_3'],
            
            // Lain-lain
            'lain_lain' => ['lain_lain', 'lainnya', 'lain'],
            'lain_lain_2' => ['lain_lain_2', 'lainnya_2', 'lain_2'],
            'lain_lain_3' => ['lain_lain_3', 'lainnya_3', 'lain_3'],
        ];
    
        // Function untuk mencari nilai berdasarkan mapping
        $findFieldValue = function($mappings) use ($fieldData, $toFloat) {
            foreach ($mappings as $key) {
                if (isset($fieldData[$key])) {
                    return $toFloat($fieldData[$key]);
                }
            }
            return 0;
        };
    
        // Isi array berdasarkan data yang ada
        $transportasiDarat[0] = $findFieldValue($fieldMappings['transportasi_darat']);
        $transportasiDarat[1] = $findFieldValue($fieldMappings['transportasi_darat_2']);
        $transportasiDarat[2] = $findFieldValue($fieldMappings['transportasi_darat_3']);
        
        $transportasiUdara[0] = $findFieldValue($fieldMappings['transportasi_udara_1']);
        $transportasiUdara[1] = $findFieldValue($fieldMappings['transportasi_udara_2']);
        $transportasiUdara[2] = $findFieldValue($fieldMappings['transportasi_udara_3']);
    
        $transportasiTaxi[0] = $findFieldValue($fieldMappings['transportasi_taxi']);
        $transportasiTaxi[1] = $findFieldValue($fieldMappings['transportasi_taxi_2']);
        $transportasiTaxi[2] = $findFieldValue($fieldMappings['transportasi_taxi_3']);
    
        $hotelBiaya[0] = $findFieldValue($fieldMappings['hotel_biaya']);
        $hotelBiaya[1] = $findFieldValue($fieldMappings['hotel_biaya_2']);
        $hotelBiaya[2] = $findFieldValue($fieldMappings['hotel_biaya_3']);
    
        $makanBiaya[0] = $findFieldValue($fieldMappings['makan_biaya']);
        $makanBiaya[1] = $findFieldValue($fieldMappings['makan_biaya_2']);
        $makanBiaya[2] = $findFieldValue($fieldMappings['makan_biaya_3']);
    
        $uangSaku[0] = $findFieldValue($fieldMappings['uang_saku']);
        $uangSaku[1] = $findFieldValue($fieldMappings['uang_saku_2']);
        $uangSaku[2] = $findFieldValue($fieldMappings['uang_saku_3']);
    
        $telephoneFax[0] = $findFieldValue($fieldMappings['telephone_fax']);
        $telephoneFax[1] = $findFieldValue($fieldMappings['telephone_fax_2']);
        $telephoneFax[2] = $findFieldValue($fieldMappings['telephone_fax_3']);
    
        $entertainment[0] = $findFieldValue($fieldMappings['entertainment']);
        $entertainment[1] = $findFieldValue($fieldMappings['entertainment_2']);
        $entertainment[2] = $findFieldValue($fieldMappings['entertainment_3']);
    
        $dokumentasi[0] = $findFieldValue($fieldMappings['dokumentasi']);
        $dokumentasi[1] = $findFieldValue($fieldMappings['dokumentasi_2']);
        $dokumentasi[2] = $findFieldValue($fieldMappings['dokumentasi_3']);
    
        $lainLain[0] = $findFieldValue($fieldMappings['lain_lain']);
        $lainLain[1] = $findFieldValue($fieldMappings['lain_lain_2']);
        $lainLain[2] = $findFieldValue($fieldMappings['lain_lain_3']);
    
        // Hitung total per kategori dengan hari yang berbeda untuk setiap perjalanan
        $totalTransportasiDarat = array_sum($transportasiDarat);
        $totalTransportasiTaxi = array_sum($transportasiTaxi);
        
        // Hotel dan Makan dihitung per perjalanan dengan hari masing-masing
        $totalHotelPerPerjalanan = [
            $hotelBiaya[0] * max(0, $hariPerPerjalanan[0] - 1),
            $hotelBiaya[1] * max(0, $hariPerPerjalanan[1] - 1),
            $hotelBiaya[2] * max(0, $hariPerPerjalanan[2] - 1),
        ];

        
        $totalMakanPerPerjalanan = [
            $makanBiaya[0] * $hariPerPerjalanan[0],
            $makanBiaya[1] * $hariPerPerjalanan[1],
            $makanBiaya[2] * $hariPerPerjalanan[2]
        ];
        
        $totalHotel = array_sum($totalHotelPerPerjalanan);
        $totalMakan = array_sum($totalMakanPerPerjalanan);
        
        $totalUangSaku = array_sum($uangSaku);
        $totalTelephoneFax = array_sum($telephoneFax);
        $totalEntertainment = array_sum($entertainment);
        $totalDokumentasi = array_sum($dokumentasi);
        $totalLainLain = array_sum($lainLain);
    
        // Hitung total per perjalanan dengan hari yang berbeda
        $totalPerjalanan = [];
        for ($i = 0; $i < 3; $i++) {
            $totalPerjalanan[$i] = 
                $transportasiDarat[$i] + 
                $transportasiTaxi[$i] + 
                $transportasiUdara[$i] +
                ($hotelBiaya[$i] * max(0, $hariPerPerjalanan[$i] - 1)) + 
                ($makanBiaya[$i] * $hariPerPerjalanan[$i]) + 
                $uangSaku[$i] + 
                $telephoneFax[$i] + 
                $entertainment[$i] + 
                $dokumentasi[$i] + 
                $lainLain[$i];
        }

    
        $grandTotal = array_sum($totalPerjalanan);
    
        // Data perjalanan detail - sesuaikan dengan field name yang ada
        $detailPerjalanan = [];
        for ($i = 1; $i <= 3; $i++) {
            $perjalananKeys = [
                "perjalanan{$i}_tanggal",
                "tanggal_perjalanan_{$i}",
                "tgl_perjalanan_{$i}",
                "perjalanan_{$i}_tanggal"
            ];
            
            $daerahKeys = [
                "perjalanan{$i}_daerah",
                "daerah_perjalanan_{$i}",
                "daerah_{$i}",
                "perjalanan_{$i}_daerah"
            ];
            
            $salesRateKeys = [
                "perjalanan{$i}_sales_rate",
                "sales_rate_{$i}",
                "perjalanan_{$i}_sales_rate"
            ];
            
            $estimasiKeys = [
                "perjalanan{$i}_estimasi",
                "estimasi_{$i}",
                "perjalanan_{$i}_estimasi"
            ];
            
            $outletKeys = [
                "perjalanan{$i}_outlet",
                "outlet_{$i}",
                "perjalanan_{$i}_outlet"
            ];
    
            // Ambil tanggal untuk field ini
            $tanggalValue = '';
            foreach ($perjalananKeys as $key) {
                if (isset($fieldData[$key]) && $fieldData[$key] !== '') {
                    $tanggalValue = $fieldData[$key];
                    break;
                }
            }
    
            $detailPerjalanan[$i] = [
                'tanggal' => $tanggalValue ?: '-',
                'daerah' => $findFieldValue($daerahKeys) ?: ($fieldData[array_values($daerahKeys)[0]] ?? '-'),
                'sales_rate' => $findFieldValue($salesRateKeys),
                'estimasi' => $findFieldValue($estimasiKeys),
                'outlet' => $findFieldValue($outletKeys),
                'hari' => $hariPerPerjalanan[$i-1] // Tambahkan info hari untuk reference
            ];
            
            // Fallback untuk daerah jika tidak ditemukan
            if ($detailPerjalanan[$i]['daerah'] === '-' || $detailPerjalanan[$i]['daerah'] === 0) {
                foreach ($daerahKeys as $key) {
                    if (isset($fieldData[$key]) && $fieldData[$key] !== '') {
                        $detailPerjalanan[$i]['daerah'] = $fieldData[$key];
                        break;
                    }
                }
            }
        }
    
        $totalSalesRate = array_sum(array_column($detailPerjalanan, 'sales_rate'));
        $totalEstimasi = array_sum(array_column($detailPerjalanan, 'estimasi'));
        $totalOutlet = array_sum(array_column($detailPerjalanan, 'outlet'));
    
        $result = [
            'transportasi_darat' => $transportasiDarat,
            'transportasi_udara' => $transportasiUdara,
            'transportasi_taxi' => $transportasiTaxi,
            'hotel_biaya' => $hotelBiaya,
            'makan_biaya' => $makanBiaya,
            'uang_saku' => $uangSaku,
            'telephone_fax' => $telephoneFax,
            'entertainment' => $entertainment,
            'dokumentasi' => $dokumentasi,
            'lain_lain' => $lainLain,
            'total_transportasi_darat' => $totalTransportasiDarat,
            'total_transportasi_udara' => array_sum($transportasiUdara),
            'total_transportasi_taxi' => $totalTransportasiTaxi,
            'total_hotel' => $totalHotel,
            'total_makan' => $totalMakan,
            'total_uang_saku' => $totalUangSaku,
            'total_telephone_fax' => $totalTelephoneFax,
            'total_entertainment' => $totalEntertainment,
            'total_dokumentasi' => $totalDokumentasi,
            'total_lain_lain' => $totalLainLain,
            'total_perjalanan' => $totalPerjalanan,
            'grand_total' => $grandTotal,
            'detail_perjalanan' => $detailPerjalanan,
            'total_sales_rate' => $totalSalesRate,
            'total_estimasi' => $totalEstimasi,
            'total_outlet' => $totalOutlet,
            'hari_per_perjalanan' => $hariPerPerjalanan, // Data hari per perjalanan
            'total_hotel_per_perjalanan' => $totalHotelPerPerjalanan, // Total hotel per perjalanan
            'total_makan_per_perjalanan' => $totalMakanPerPerjalanan  // Total makan per perjalanan
        ];
        
        return $result;
    }
    
    public function selesai()
    {
        // Ambil data kategori pengajuan
        $kategoriPengajuan = KategoriPengajuan::aktif()
            ->orderBy('nama')
            ->get();

        // Ambil data pengajuan hanya milik user yang sedang login
        $pengajuanList = Pengajuan::with([
            'kategoriPengajuan',
            'requester',
            'settlement',
            'transactionRequest',
            'emailNotificationLogs', // Tambahkan untuk monitoring email
            'historyPengajuan' => function($query) {
                $query->latest('created_at');
            }
        ])
        ->where('requester_id', Auth::id())
        ->orderBy('created_at', 'desc')
        ->get();

        return view('Approval-app.Karyawan.Pengajuan.selesai', 
            compact('kategoriPengajuan', 'pengajuanList'));
    }

    // Method untuk mengambil detail pengajuan via AJAX
    public function getDetailPengajuan($id)
    {
        $pengajuan = Pengajuan::with([
            'kategoriPengajuan',
            'requester',
            'settlement',
            'historyPengajuan',
            'detailPengajuan.formField'
        ])->find($id);
    
        if (!$pengajuan) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
        }
    
        // Query manual untuk mengambil progress approval dengan settlement_id = null
        $progressApprovals = ProgressApproval::with(['approver', 'flowApproval'])
            ->where('pengajuan_id', $id)
            ->whereNull('settlement_id')
            ->orderBy('urutan')
            ->get();
    
        // PERBAIKAN UTAMA: Transformasi data detail pengajuan dengan jumlah hari
        $detailFields = [];
        foreach ($pengajuan->detailPengajuan as $detail) {
            // Pastikan formField tidak null
            if (!$detail->formField) {
                continue;
            }
            
            $detailFields[] = [
                'name' => $detail->formField->nama_field,
                'label' => $detail->formField->label,
                'type' => $detail->formField->tipe_field,
                'value' => $detail->nilai,
                'jumlah_hari' => $detail->jumlah_hari, // TAMBAHAN: Include jumlah hari
                'urutan' => $detail->formField->urutan
            ];
        }
    
        usort($detailFields, function($a, $b) {
            return $a['urutan'] <=> $b['urutan'];
        });
    
        // TAMBAHAN BARU: Hitung data khusus untuk hotel dan makan berdasarkan jumlah hari tersimpan
        $hotelMakanData = $this->calculateHotelMakanData($pengajuan->detailPengajuan);
    
        // Transformasi data progress approval untuk timeline
        $progressData = [];
        foreach ($progressApprovals as $progress) {
            $progressData[] = [
                'urutan' => $progress->urutan,
                'step_name' => $progress->step_name,
                'approver_name' => $progress->approver ? $progress->approver->nama : 'Belum ditentukan',
                'approver_jabatan' => $progress->approver ? $progress->approver->jabatan : 'Belum ditentukan',
                'approver_email' => $progress->approver ? $progress->approver->email : null,
                'status' => $progress->status,
                'tanggal_approval' => $progress->tanggal_approval,
                'catatan' => $progress->catatan,
                'is_current' => ($progress->urutan == $pengajuan->current_step),
                'is_completed' => in_array($progress->status, ['approved', 'completed']),
                'is_rejected' => ($progress->status == 'rejected')
            ];
        }
    
        $pengajuan->detail_fields = $detailFields;
        $pengajuan->progress_data = $progressData;
        $pengajuan->hotel_makan_data = $hotelMakanData; // TAMBAHAN: Data kalkulasi hotel dan makan
    
        return response()->json(['success' => true, 'data' => $pengajuan]);
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

    public function getDetailSettlement($id)
    {
        try {
            $settlement = Settlement::with([
                'details',
                'pengajuan.requester'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $settlement
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }
    // Akhir logika controller untuk halaman index
    
     // Logika controller untuk halaman create atau halaman memilih kategori pengajuan dan membuat pengajuan
    public function create()
    {
        $karyawanId = Auth::user()->id;
        
        // Cek jumlah pengajuan yang masih dalam proses
        $pengajuanBelumSelesai = Pengajuan::where('requester_id', $karyawanId)
            ->whereIn('status_pengajuan', ['proses', 'proses_settlement', 'settlement_created'])
            ->whereNotIn('statuspembayaran', ['Dibayarkan']) // Belum dibayarkan
            ->count();
            
        // Jika sudah ada 3 atau lebih pengajuan yang belum selesai, tidak bisa buat pengajuan baru
        if ($pengajuanBelumSelesai >= 3) {
            // Get detail pengajuan yang masih pending untuk ditampilkan
            $pengajuanPending = Pengajuan::where('requester_id', $karyawanId)
                ->whereIn('status_pengajuan', ['proses','proses_settlement', 'settlement_created'])
                ->whereNotIn('statuspembayaran', ['Dibayarkan'])
                ->with('kategoriPengajuan')
                ->orderBy('created_at', 'desc')
                ->get();
                
            return view('Approval-app.Karyawan.Pengajuan.create-blocked', 
                compact('pengajuanPending', 'pengajuanBelumSelesai'));
        }

        $kategoriPengajuan = KategoriPengajuan::aktif()
            ->orderBy('nama')
            ->get();

        return view('Approval-app.Karyawan.Pengajuan.create', 
            compact('kategoriPengajuan', 'pengajuanBelumSelesai'));
    }
    
    // Method untuk cek status pengajuan via AJAX (opsional)
    public function checkPengajuanStatus()
    {
        $karyawanId = Auth::user()->id;
        
        $pengajuanBelumSelesai = Pengajuan::where('requester_id', $karyawanId)
            ->whereIn('status_pengajuan', ['proses','proses_settlement', 'settlement_created'])
            ->whereNotIn('statuspembayaran', ['Dibayarkan'])
            ->count();
            
        return response()->json([
            'can_create' => $pengajuanBelumSelesai < 3,
            'pending_count' => $pengajuanBelumSelesai,
            'max_allowed' => 3
        ]);
    }

    // logic untuk Ambil form fields berdasarkan kategori, urutkan berdasarkan posisi
    public function getFormFields($id)
    {
        try {
            $kategori = KategoriPengajuan::findOrFail($id);
            
            // Ambil form fields berdasarkan kategori, urutkan berdasarkan posisi
            $formFields = FormField::where('kategori_pengajuan_id', $id)
                ->where('status', 'aktif')
                ->orderBy('posisi_row')
                ->orderBy('posisi_col')
                ->get();
            
            // Kelompokkan berdasarkan row position
            $groupedFields = [];
            foreach ($formFields as $field) {
                $row = $field->posisi_row ?? 1;
                if (!isset($groupedFields[$row])) {
                    $groupedFields[$row] = [];
                }
                $groupedFields[$row][] = $field;
            }
            
            return response()->json([
                'success' => true,
                'data' => $groupedFields,
                'kategori' => [
                    'id' => $kategori->id,
                    'nama' => $kategori->nama,
                    'kode' => $kategori->kode
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat form fields: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function store(Request $request)
    {
        $emailStatus = [
            'success' => false,
            'messages' => [],
            'errors' => []
        ];
    
        try {
            // Validasi dasar dengan tambahan validasi date range
            $validator = Validator::make($request->all(), [
                'kategori_pengajuan_id' => 'required|exists:KategoriPengajuan,id',
                'deskripsi' => 'nullable|string',
                'file_pendukung.*' => 'nullable|file|max:10240',
                // Validasi date range untuk perjalanan
                'form_data.nama_karyawan' => 'required|string|max:255', 
                'form_data.area' => 'required|string|max:255',
                
                'form_data.perjalanan1_tanggal_dari' => 'nullable|date',
                'form_data.perjalanan1_tanggal_sampai' => 'nullable|date|after_or_equal:form_data.perjalanan1_tanggal_dari',
                'form_data.perjalanan2_tanggal_dari' => 'nullable|date',
                'form_data.perjalanan2_tanggal_sampai' => 'nullable|date|after_or_equal:form_data.perjalanan2_tanggal_dari',
                'form_data.perjalanan3_tanggal_dari' => 'nullable|date',
                'form_data.perjalanan3_tanggal_sampai' => 'nullable|date|after_or_equal:form_data.perjalanan3_tanggal_dari',
            ], [
                'form_data.nama_karyawan.required' => 'Nama Karyawan wajib diisi',
                'form_data.area.required' => 'Area wajib diisi',
                // Custom error messages untuk date range
                'form_data.perjalanan1_tanggal_sampai.after_or_equal' => 'Tanggal selesai perjalanan 1 harus sama atau setelah tanggal mulai',
                'form_data.perjalanan2_tanggal_sampai.after_or_equal' => 'Tanggal selesai perjalanan 2 harus sama atau setelah tanggal mulai',
                'form_data.perjalanan3_tanggal_sampai.after_or_equal' => 'Tanggal selesai perjalanan 3 harus sama atau setelah tanggal mulai',
            ]);
    
            // Validasi tambahan untuk memastikan jika ada tanggal dari, maka harus ada tanggal sampai
            $validator->after(function ($validator) use ($request) {
                for ($i = 1; $i <= 3; $i++) {
                    $tanggalDari = $request->input("form_data.perjalanan{$i}_tanggal_dari");
                    $tanggalSampai = $request->input("form_data.perjalanan{$i}_tanggal_sampai");

                    // Jika ada tanggal dari tapi tidak ada tanggal sampai
                    if (!empty($tanggalDari) && empty($tanggalSampai)) {
                        $validator->errors()->add("form_data.perjalanan{$i}_tanggal_sampai", "Tanggal selesai perjalanan {$i} harus diisi jika tanggal mulai sudah diisi");
                    }

                    // Jika ada tanggal sampai tapi tidak ada tanggal dari
                    if (empty($tanggalDari) && !empty($tanggalSampai)) {
                        $validator->errors()->add("form_data.perjalanan{$i}_tanggal_dari", "Tanggal mulai perjalanan {$i} harus diisi jika tanggal selesai sudah diisi");
                    }
                }
            });

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: Mohon lengkapi Nama dan Area.',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            DB::beginTransaction();
    
            // Upload file pendukung
            $filePaths = [];
            if ($request->hasFile('file_pendukung')) {
                foreach ($request->file('file_pendukung') as $file) {
                    $path = $file->store('pengajuan/files', 'custom_public');
                    $filePaths[] = $path;
                }
            }
    
            // Dapatkan requester dan department
            $requester = Auth::user();
            $karyawan = Karyawan::find($requester->id);
    
            if (!$karyawan) {
                throw new \Exception('Data karyawan tidak ditemukan');
            }
    
            // Cek keberadaan FlowApproval terlebih dahulu
            $flowApprovals = FlowApproval::forKategori($request->kategori_pengajuan_id)
                ->forRequester($karyawan->id)
                ->where('status', 'aktif')
                ->orderBy('urutan')
                ->get();
    
            // Jika tidak ada FlowApproval yang ditemukan
            if ($flowApprovals->isEmpty()) {
                throw new \Exception('Flow approval belum dikonfigurasi untuk kategori pengajuan ini. Silakan hubungi administrator untuk mengatur flow approval terlebih dahulu.');
            }
    
            $totalSteps = $flowApprovals->count();
    
            // SATU-SATUNYA PERUBAHAN: Generate nomor pengajuan dengan MAX (bukan count)
            $nomorPengajuan = $this->generateNomorPengajuanSafe($request->kategori_pengajuan_id);
    
            $kategori = KategoriPengajuan::find($request->kategori_pengajuan_id);
            $judul = $request->judul ?? $kategori->nama;
            $isSettlementRequired = $kategori->settlement == 1 || $request->has('is_settlement_required');
    
            // Hitung nominal pengajuan dari form currency
            $nominalPengajuan = $this->calculateNominalFromCurrencyFields($request);
    
            // Buat pengajuan baru - TIDAK ADA PERUBAHAN STRUKTUR
            $pengajuan = Pengajuan::create([
                'nomor_pengajuan' => $nomorPengajuan,
                'kategori_pengajuan_id' => $request->kategori_pengajuan_id,
                'requester_id' => $karyawan->id,
                'judul' => $judul,
                'deskripsi' => $request->deskripsi ?? null,
                'nominal_pengajuan' => $nominalPengajuan,
                'tanggal_pengajuan' => now(),
                'tanggal_kebutuhan' => $request->tanggal_kebutuhan ?? null,
                'status_pengajuan' => 'proses',
                'current_step' => $totalSteps > 0 ? 1 : 0,
                'total_step' => $totalSteps,
                'catatan_requester' => $request->catatan_requester ?? null,
                'file_pendukung' => $filePaths,
                'is_settlement_required' => $isSettlementRequired
            ]);
    
            // Buat progress approval records berdasarkan FlowApproval yang ada
            foreach ($flowApprovals as $index => $flowApproval) {
                ProgressApproval::create([
                    'pengajuan_id' => $pengajuan->id,
                    'flow_approval_id' => $flowApproval->id,
                    'requester_id' => $karyawan->id,
                    'approver_id' => $flowApproval->approver_id,
                    'step_name' => $flowApproval->nama_step,
                    'urutan' => $flowApproval->urutan,
                    'status' => $index == 0 ? 'proses' : 'pending',
                    'step_type' => 'approval'
                ]);
            }
    
            // Simpan detail form fields dengan handling khusus untuk date range
            if ($request->has('form_data')) {
                foreach ($request->form_data as $fieldName => $value) {
                    // Skip jika value kosong dan bukan field wajib
                    if (empty($value) && $value !== 0 && $value !== '0') {
                        continue;
                    }
    
                    $formField = FormField::where('kategori_pengajuan_id', $request->kategori_pengajuan_id)
                        ->where('nama_field', $fieldName)
                        ->first();
    
                    // Handle date range fields dengan jumlah hari
                    if (strpos($fieldName, '_tanggal_dari') !== false) {
                        $baseFieldName = str_replace('_tanggal_dari', '_tanggal', $fieldName);
                        $tanggalSampaiFieldName = str_replace('_tanggal_dari', '_tanggal_sampai', $fieldName);
                        $tanggalSampai = $request->input("form_data.{$tanggalSampaiFieldName}");
    
                        if (!empty($value) && !empty($tanggalSampai)) {
                            $dateRangeValue = $value . ' - ' . $tanggalSampai;
    
                            // HITUNG JUMLAH HARI DARI DATE RANGE
                            $startDate = new \DateTime($value);
                            $endDate = new \DateTime($tanggalSampai);
                            $interval = $startDate->diff($endDate);
                            $jumlahHari = $interval->days + 1; // +1 untuk include hari pertama
    
                            // Cari form field untuk date range
                            $dateRangeField = FormField::where('kategori_pengajuan_id', $request->kategori_pengajuan_id)
                                ->where('nama_field', $baseFieldName)
                                ->first();
    
                            if ($dateRangeField) {
                                DetailPengajuan::create([
                                    'pengajuan_id' => $pengajuan->id,
                                    'form_field_id' => $dateRangeField->id,
                                    'nilai' => $dateRangeValue,
                                    'jumlah_hari' => $jumlahHari
                                ]);
                            }
                        }
                    }
                    // Skip field tanggal_sampai karena sudah diproses di tanggal_dari
                    elseif (strpos($fieldName, '_tanggal_sampai') !== false) {
                        continue;
                    }
                    // Skip hidden field yang tidak ada di FormField
                    elseif (strpos($fieldName, '_jumlah_hari') !== false || strpos($fieldName, '_jumlah_malam') !== false) {
                        // Skip karena ini hidden field untuk kalkulasi saja
                        continue;
                    }
                    // Handle hotel dengan jumlah malam
                    elseif (strpos($fieldName, 'hotel_biaya') !== false && $formField) {
                        // Ekstrak nomor perjalanan
                        $perjalananNumber = '1'; // default
                        if (preg_match('/hotel_biaya(_(\d+))?/', $fieldName, $matches)) {
                            $perjalananNumber = isset($matches[2]) ? $matches[2] : '1';
                        }
    
                        // Cari data tanggal untuk perjalanan ini
                        $tanggalDari = $request->input("form_data.perjalanan{$perjalananNumber}_tanggal_dari");
                        $tanggalSampai = $request->input("form_data.perjalanan{$perjalananNumber}_tanggal_sampai");
    
                        $jumlahMalam = 0;
                        if (!empty($tanggalDari) && !empty($tanggalSampai)) {
                            $startDate = new \DateTime($tanggalDari);
                            $endDate = new \DateTime($tanggalSampai);
                            $interval = $startDate->diff($endDate);
                            $jumlahMalam = $interval->days; // Malam = hari - 1 (tidak +1)
                        }
    
                        DetailPengajuan::create([
                            'pengajuan_id' => $pengajuan->id,
                            'form_field_id' => $formField->id,
                            'nilai' => $value,
                            'jumlah_hari' => $jumlahMalam // Simpan jumlah malam untuk hotel
                        ]);
                    }
                    // Handle makan dengan jumlah hari
                    elseif (strpos($fieldName, 'makan_biaya') !== false && $formField) {
                        // Ekstrak nomor perjalanan
                        $perjalananNumber = '1'; // default
                        if (preg_match('/makan_biaya(_(\d+))?/', $fieldName, $matches)) {
                            $perjalananNumber = isset($matches[2]) ? $matches[2] : '1';
                        }
    
                        // Cari data tanggal untuk perjalanan ini
                        $tanggalDari = $request->input("form_data.perjalanan{$perjalananNumber}_tanggal_dari");
                        $tanggalSampai = $request->input("form_data.perjalanan{$perjalananNumber}_tanggal_sampai");
    
                        $jumlahHari = 0;
                        if (!empty($tanggalDari) && !empty($tanggalSampai)) {
                            $startDate = new \DateTime($tanggalDari);
                            $endDate = new \DateTime($tanggalSampai);
                            $interval = $startDate->diff($endDate);
                            $jumlahHari = $interval->days + 1; // +1 untuk include hari pertama
                        }
    
                        DetailPengajuan::create([
                            'pengajuan_id' => $pengajuan->id,
                            'form_field_id' => $formField->id,
                            'nilai' => $value,
                            'jumlah_hari' => $jumlahHari // Simpan jumlah hari untuk makan
                        ]);
                    }
                    // HANDLE FIELD LAINNYA YANG ADA DI FormField
                    elseif ($formField) {
                        DetailPengajuan::create([
                            'pengajuan_id' => $pengajuan->id,
                            'form_field_id' => $formField->id,
                            'nilai' => is_array($value) ? json_encode($value) : $value,
                            'jumlah_hari' => null // Default null untuk field lainnya
                        ]);
                    }
                    // SKIP field yang tidak ada di FormField untuk menghindari error
                    else {
                        \Log::info("Field tidak ditemukan di FormField: {$fieldName}", [
                            'kategori_pengajuan_id' => $request->kategori_pengajuan_id,
                            'field_name' => $fieldName,
                            'value' => $value
                        ]);
                        continue;
                    }
                }
            }
    
            // Simpan history pengajuan
            $firstStep = $flowApprovals->first();
            $description = "Pengajuan {$pengajuan->nomor_pengajuan} dibuat dengan kategori {$kategori->nama}";
            if ($nominalPengajuan > 0) {
                $description .= " dengan nominal Rp " . number_format($nominalPengajuan, 2, ',', '.');
            }
    
            HistoryPengajuan::createHistory(
                $pengajuan->id,
                'created',
                null, // status_before null karena baru dibuat
                'proses',
                $karyawan->id,
                $description,
                $request->catatan_requester,
                $firstStep ? $firstStep->nama_step : null,
                1
            );
    
            DB::commit();
    
            // KIRIM EMAIL NOTIFIKASI DENGAN MONITORING
            \App\Helpers\ActivityLogger::log('Create Pengajuan', "User created pengajuan {$pengajuan->nomor_pengajuan}");
            $emailStatus = $this->sendEmailWithMonitoring($pengajuan);
    
            // Siapkan response message
            $mainMessage = 'Pengajuan berhasil disimpan';
            if ($nominalPengajuan > 0) {
                $mainMessage .= ' dengan nominal Rp ' . number_format($nominalPengajuan, 2, ',', '.');
            }
    
            $emailMessage = '';
            $alertType = 'success';
    
            if ($emailStatus['success']) {
                $emailMessage = ' dan notifikasi email berhasil dikirim';
                $alertType = 'success';
            } else {
                $emailMessage = ', namun ada masalah dengan pengiriman email';
                $alertType = 'warning';
            }
    
            return response()->json([
                'success' => true,
                'message' => $mainMessage . $emailMessage,
                'alert_type' => $alertType,
                'email_status' => $emailStatus,
                'redirect' => route('pengajuan.index'),
                'data' => [
                    'id' => $pengajuan->id,
                    'nomor_pengajuan' => $pengajuan->nomor_pengajuan,
                    'status' => $pengajuan->status_pengajuan,
                    'nominal' => $nominalPengajuan,
                    'total_steps' => $totalSteps
                ]
            ]);
    
        } catch (\Exception $e) {
            DB::rollback();
    
            // Log error detail
            \Log::error('Error saat menyimpan pengajuan:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'alert_type' => 'error',
                'email_status' => $emailStatus
            ], 500);
        }
    }
    
    /**
     * FUNGSI BARU: Menghitung nominal pengajuan dari currency fields
     */
    private function calculateNominalFromCurrencyFields(Request $request)
    {
        $total = 0;
        
        if ($request->has('form_data')) {
            foreach ($request->form_data as $fieldName => $value) {
                // Skip non-numeric values
                if (!is_numeric($value) || empty($value)) continue;
                
                $numericValue = (float)$value;
                
                // HOTEL: multiply dengan jumlah malam
                if (strpos($fieldName, 'hotel_biaya') !== false) {
                    // Ekstrak nomor perjalanan
                    $perjalananNumber = '1'; // default
                    if (preg_match('/hotel_biaya(_(\d+))?/', $fieldName, $matches)) {
                        $perjalananNumber = isset($matches[2]) ? $matches[2] : '1';
                    }
                    
                    // Cari data tanggal untuk perjalanan ini
                    $tanggalDari = $request->input("form_data.perjalanan{$perjalananNumber}_tanggal_dari");
                    $tanggalSampai = $request->input("form_data.perjalanan{$perjalananNumber}_tanggal_sampai");
                    
                    $jumlahMalam = 0;
                    if (!empty($tanggalDari) && !empty($tanggalSampai)) {
                        $startDate = new \DateTime($tanggalDari);
                        $endDate = new \DateTime($tanggalSampai);
                        $interval = $startDate->diff($endDate);
                        $jumlahMalam = $interval->days;
                    }
                    
                    if ($jumlahMalam > 0) {
                        $total += $numericValue * $jumlahMalam;
                    } else {
                        $total += $numericValue; // Fallback jika tidak ada jumlah malam
                    }
                }
                // MAKAN: multiply dengan jumlah hari
                elseif (strpos($fieldName, 'makan_biaya') !== false) {
                    // Ekstrak nomor perjalanan
                    $perjalananNumber = '1'; // default
                    if (preg_match('/makan_biaya(_(\d+))?/', $fieldName, $matches)) {
                        $perjalananNumber = isset($matches[2]) ? $matches[2] : '1';
                    }
                    
                    // Cari data tanggal untuk perjalanan ini
                    $tanggalDari = $request->input("form_data.perjalanan{$perjalananNumber}_tanggal_dari");
                    $tanggalSampai = $request->input("form_data.perjalanan{$perjalananNumber}_tanggal_sampai");
                    
                    $jumlahHari = 0;
                    if (!empty($tanggalDari) && !empty($tanggalSampai)) {
                        $startDate = new \DateTime($tanggalDari);
                        $endDate = new \DateTime($tanggalSampai);
                        $interval = $startDate->diff($endDate);
                        $jumlahHari = $interval->days + 1;
                    }
                    
                    if ($jumlahHari > 0) {
                        $total += $numericValue * $jumlahHari;
                    } else {
                        $total += $numericValue; // Fallback jika tidak ada jumlah hari
                    }
                }
                // FIELD LAINNYA: langsung ditambahkan (transportasi, uang saku, dll)
                elseif (in_array(true, [
                    strpos($fieldName, 'transportasi_darat') !== false, // Cek Darat
                    strpos($fieldName, 'transportasi_taxi') !== false, // Cek Taxi
                    strpos($fieldName, 'transportasi_udara') !== false, // <-- PENAMBAHAN BARU
                    strpos($fieldName, 'uang_saku') !== false,

                    strpos($fieldName, 'telephone_fax') !== false,
                    strpos($fieldName, 'entertainment') !== false,
                    strpos($fieldName, 'dokumentasi') !== false,
                    strpos($fieldName, 'lain_lain') !== false,
                ])) {
                    $total += $numericValue;
                }
            }
        }
        
        return $total;
    }
    
    private function calculatePerjalananDinasTotal(Request $request)
    {
        if (!$request->has('form_data')) {
            return 0;
        }
        
        $formData = $request->form_data;
        $total = 0;
        
        // Transportasi
        $total += floatval($formData['transportasi_darat'] ?? 0);
        $total += floatval($formData['transportasi_taxi'] ?? 0);
        
        // Hotel (tarif × hari)
        $hotelRate = floatval($formData['hotel_biaya'] ?? 0);
        $hotelDays = floatval($formData['hotel_jumlah_hari'] ?? 1);
        if ($hotelDays <= 0) $hotelDays = 1;
        $total += ($hotelRate * $hotelDays);
        
        // Makan (tarif × hari)
        $makanRate = floatval($formData['makan_biaya'] ?? 0);
        $makanDays = floatval($formData['makan_jumlah_hari'] ?? 1);
        if ($makanDays <= 0) $makanDays = 1;
        $total += ($makanRate * $makanDays);
        
        // Biaya lainnya
        $total += floatval($formData['uang_saku'] ?? 0);
        $total += floatval($formData['telephone_fax'] ?? 0);
        $total += floatval($formData['entertainment'] ?? 0);
        $total += floatval($formData['dokumentasi'] ?? 0);
        $total += floatval($formData['lain_lain'] ?? 0);
        
        return $total;
    }
    
    private function sendEmailWithMonitoring($pengajuan)
    {
        $status = [
            'success' => false,
            'messages' => [],
            'errors' => [],
            'email_sent_count' => 0,
            'email_failed_count' => 0
        ];

        try {
            // Load relasi yang diperlukan
            $pengajuan->load([
                'kategoriPengajuan',
                'requester.department',
                'requester.atasan',
                'progressApprovals' => function($query) {
                    $query->where('status', 'proses')
                          ->with('approver');
                }
            ]);

            // Cek konfigurasi email terlebih dahulu
            if (!$this->isEmailConfigured()) {
                $error = 'Konfigurasi email belum diatur dengan benar';
                $status['errors'][] = $error;
                $this->logEmailNotification($pengajuan->id, null, 'error', $error);
                return $status;
            }

            // Kirim ke approver yang sedang aktif
            $currentApproval = $pengajuan->progressApprovals
                ->where('status', 'proses')
                ->first();

            if ($currentApproval && $currentApproval->approver) {
                $emailResult = $this->sendSingleEmail($pengajuan, $currentApproval->approver, 'approver');
                if ($emailResult['success']) {
                    $status['email_sent_count']++;
                    $status['messages'][] = "Email berhasil dikirim ke approver: {$currentApproval->approver->nama}";
                } else {
                    $status['email_failed_count']++;
                    $status['errors'][] = "Gagal kirim email ke approver {$currentApproval->approver->nama}: {$emailResult['error']}";
                }
            } else {
                $status['errors'][] = 'Approver tidak ditemukan atau tidak memiliki email';
            }

            // Kirim ke atasan langsung (jika berbeda dengan approver)
            if ($pengajuan->requester->atasan && 
                $pengajuan->requester->atasan->email && 
                (!$currentApproval || $currentApproval->approver_id != $pengajuan->requester->atasan->id)) {
                
                $emailResult = $this->sendSingleEmail($pengajuan, $pengajuan->requester->atasan, 'atasan');
                if ($emailResult['success']) {
                    $status['email_sent_count']++;
                    $status['messages'][] = "Email berhasil dikirim ke atasan: {$pengajuan->requester->atasan->nama}";
                } else {
                    $status['email_failed_count']++;
                    $status['errors'][] = "Gagal kirim email ke atasan {$pengajuan->requester->atasan->nama}: {$emailResult['error']}";
                }
            }

            // Set status keseluruhan
            $status['success'] = $status['email_sent_count'] > 0;

            // Log summary
            $this->logEmailNotification(
                $pengajuan->id, 
                null, 
                $status['success'] ? 'success' : 'failed',
                "Email terkirim: {$status['email_sent_count']}, Gagal: {$status['email_failed_count']}"
            );

        } catch (\Exception $e) {
            $error = 'Error sistem saat mengirim email: ' . $e->getMessage();
            $status['errors'][] = $error;
            $this->logEmailNotification($pengajuan->id, null, 'error', $error);
            
            \Log::error('Email system error:', [
                'pengajuan_id' => $pengajuan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $status;
    }
    
    private function sendSingleEmail($pengajuan, $penerima, $tipe = 'approver')
    {
        $result = ['success' => false, 'error' => ''];

        try {
            // Validasi email penerima
            if (empty($penerima->email)) {
                $result['error'] = 'Email penerima kosong';
                $this->logEmailNotification($pengajuan->id, $penerima->id, 'failed', $result['error']);
                return $result;
            }

            if (!filter_var($penerima->email, FILTER_VALIDATE_EMAIL)) {
                $result['error'] = 'Format email tidak valid: ' . $penerima->email;
                $this->logEmailNotification($pengajuan->id, $penerima->id, 'failed', $result['error']);
                return $result;
            }

            // Cek apakah menggunakan queue atau langsung
            if (config('queue.default') !== 'sync') {
                // Gunakan job queue
                SendPengajuanEmailNotification::dispatch($pengajuan, $penerima, $tipe)
                    ->delay(now()->addSeconds(5));
                
                $result['success'] = true;
                $this->logEmailNotification($pengajuan->id, $penerima->id, 'queued', 'Email dijadwalkan untuk dikirim via queue');
            } else {
                // Kirim langsung
                Mail::to($penerima->email)->send(new PengajuanBaruNotification($pengajuan, $penerima, $tipe));
                
                $result['success'] = true;
                $this->logEmailNotification($pengajuan->id, $penerima->id, 'sent', 'Email berhasil dikirim');
            }

        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            $this->logEmailNotification($pengajuan->id, $penerima->id, 'failed', $result['error']);
            
            \Log::error('Error mengirim email:', [
                'pengajuan_id' => $pengajuan->id,
                'penerima_id' => $penerima->id,
                'email' => $penerima->email,
                'error' => $e->getMessage()
            ]);
        }

        return $result;
    }
    
    private function isEmailConfigured()
    {
        $requiredConfigs = ['mail.mailers.smtp.host', 'mail.from.address'];
        
        foreach ($requiredConfigs as $config) {
            if (empty(config($config))) {
                return false;
            }
        }

        return true;
    }
    
    /**
     * Log aktivitas email notification
     */
    private function logEmailNotification($pengajuanId, $recipientId, $status, $message)
    {
        try {
            EmailNotificationLog::create([
                'pengajuan_id' => $pengajuanId,
                'recipient_id' => $recipientId,
                'recipient_email' => $recipientId ? Karyawan::find($recipientId)->email ?? '' : '',
                'status' => $status, // sent, failed, queued, error
                'message' => $message,
                'sent_at' => now()
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan log email notification: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk melihat status email notifikasi
     */
    public function getEmailStatus($pengajuanId)
    {
        try {
            $pengajuan = Pengajuan::with('emailNotificationLogs.recipient')->findOrFail($pengajuanId);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'pengajuan' => $pengajuan->nomor_pengajuan,
                    'email_logs' => $pengajuan->emailNotificationLogs->map(function($log) {
                        return [
                            'recipient' => $log->recipient ? $log->recipient->nama : 'Unknown',
                            'email' => $log->recipient_email,
                            'status' => $log->status,
                            'message' => $log->message,
                            'sent_at' => $log->sent_at->format('d/m/Y H:i:s')
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }
    
    private function dispatchEmailNotifications($pengajuan)
    {
        try {
            // Load relasi yang diperlukan
            $pengajuan->load([
                'kategoriPengajuan',
                'requester.department',
                'requester.atasan',
                'progressApprovals' => function($query) {
                    $query->where('status', 'proses')
                          ->with('approver');
                }
            ]);

            // Dispatch job untuk approver yang sedang aktif
            $currentApproval = $pengajuan->progressApprovals
                ->where('status', 'proses')
                ->first();

            if ($currentApproval && $currentApproval->approver && $currentApproval->approver->email) {
                SendPengajuanEmailNotification::dispatch($pengajuan, $currentApproval->approver, 'approver')
                    ->delay(now()->addSeconds(5)); // Delay 5 detik untuk memastikan transaksi selesai
            }

            // Dispatch job untuk atasan langsung (jika berbeda dengan approver)
            if ($pengajuan->requester->atasan && 
                $pengajuan->requester->atasan->email && 
                (!$currentApproval || $currentApproval->approver_id != $pengajuan->requester->atasan->id)) {
                
                SendPengajuanEmailNotification::dispatch($pengajuan, $pengajuan->requester->atasan, 'atasan')
                    ->delay(now()->addSeconds(10)); // Delay 10 detik
            }

            \Log::info('Email jobs berhasil didispatch untuk pengajuan', [
                'pengajuan_id' => $pengajuan->id,
                'nomor_pengajuan' => $pengajuan->nomor_pengajuan
            ]);

        } catch (\Exception $e) {
            \Log::error('Gagal mendispatch email jobs: ' . $e->getMessage(), [
                'pengajuan_id' => $pengajuan->id,
                'error' => $e->getTraceAsString()
            ]);
        }
    }
    
    private function kirimEmailNotifikasiPengajuanBaru($pengajuan)
    {
        try {
            // Load relasi yang diperlukan
            $pengajuan->load([
                'kategoriPengajuan',
                'requester.department',
                'progressApprovals' => function($query) {
                    $query->where('status', 'proses')
                          ->with('approver');
                }
            ]);

            // Ambil approver yang sedang aktif (status = 'proses')
            $currentApproval = $pengajuan->progressApprovals
                ->where('status', 'proses')
                ->first();

            if ($currentApproval && $currentApproval->approver && $currentApproval->approver->email) {
                // Kirim email ke approver yang bersangkutan
                Mail::to($currentApproval->approver->email)
                    ->send(new PengajuanBaruNotification($pengajuan, $currentApproval->approver));

                \Log::info('Email notifikasi pengajuan baru berhasil dikirim', [
                    'pengajuan_id' => $pengajuan->id,
                    'approver_email' => $currentApproval->approver->email,
                    'nomor_pengajuan' => $pengajuan->nomor_pengajuan
                ]);
            }

            // Kirim juga ke atasan langsung jika ada
            if ($pengajuan->requester->atasan && $pengajuan->requester->atasan->email) {
                Mail::to($pengajuan->requester->atasan->email)
                    ->send(new PengajuanBaruNotification($pengajuan, $pengajuan->requester->atasan, 'atasan'));

                \Log::info('Email notifikasi pengajuan baru berhasil dikirim ke atasan', [
                    'pengajuan_id' => $pengajuan->id,
                    'atasan_email' => $pengajuan->requester->atasan->email,
                    'nomor_pengajuan' => $pengajuan->nomor_pengajuan
                ]);
            }

        } catch (\Exception $e) {
            // Log error tapi jangan gagalkan proses utama
            \Log::error('Gagal mengirim email notifikasi pengajuan baru: ' . $e->getMessage(), [
                'pengajuan_id' => $pengajuan->id,
                'error' => $e->getTraceAsString()
            ]);
        }
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::findOrFail($id);
            $currentStep = $pengajuan->current_step;
            
            Log::info("Processing approval for pengajuan: {$pengajuan->nomor_pengajuan}, current step: {$currentStep}, total steps: {$pengajuan->total_step}");
            
            // Cari progress approval untuk step saat ini
            $progressApproval = ProgressApproval::where('pengajuan_id', $pengajuan->id)
                ->where('urutan', $currentStep)
                ->where('status', 'proses')
                ->first();

            if (!$progressApproval) {
                throw new \Exception('Step approval tidak ditemukan atau sudah diproses');
            }

            // Validasi apakah user berhak approve
            $approver = Karyawan::find(Auth::user()->id);
            if (!$this->canApprove($progressApproval, $approver)) {
                throw new \Exception('Anda tidak memiliki wewenang untuk approve step ini');
            }

            // Update progress approval
            $progressApproval->update([
                'approver_id' => $approver->id,
                'status' => 'approved',
                'tanggal_approval' => now(),
                'catatan' => $request->catatan ?? null
            ]);

            // Simpan history
            HistoryPengajuan::create([
                'pengajuan_id' => $pengajuan->id,
                'step_ke' => $currentStep,
                'approver_id' => $approver->id,
                'aksi' => 'approve',
                'status_sebelum' => $pengajuan->status_pengajuan,
                'status_sesudah' => $currentStep < $pengajuan->total_step ? 'proses' : 'approved',
                'catatan' => $request->catatan ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Cek apakah ini step terakhir
            if ($currentStep >= $pengajuan->total_step) {
                Log::info("Final approval reached for pengajuan: {$pengajuan->nomor_pengajuan}");
                
                // Update pengajuan menjadi disetujui
                $pengajuan->update([
                    'status_pengajuan' => 'approved'
                ]);

                Log::info("Pengajuan status updated to approved. Settlement required: " . ($pengajuan->is_settlement_required ? 'yes' : 'no'));
                Log::info("Current settlement_id: " . ($pengajuan->settlement_id ?? 'null'));

                // AUTO-CREATE SETTLEMENT jika diperlukan
                if ($pengajuan->is_settlement_required && !$pengajuan->settlement_id) {
                    Log::info("Creating settlement for pengajuan: {$pengajuan->nomor_pengajuan}");
                    
                    try {
                        $settlement = $this->createSettlement($pengajuan);
                        
                        // Update pengajuan dengan settlement_id
                        $pengajuan->update(['settlement_id' => $settlement->id]);
                        
                        Log::info("Settlement created successfully: {$settlement->nomor_settlement}");
                    } catch (\Exception $e) {
                        Log::error("Failed to create settlement: " . $e->getMessage());
                        // Jangan throw exception, biarkan approval tetap berhasil
                    }
                } else {
                    Log::info("Settlement not created. Required: " . ($pengajuan->is_settlement_required ? 'yes' : 'no') . ", Already exists: " . ($pengajuan->settlement_id ? 'yes' : 'no'));
                }
            } else {
                // Lanjut ke step berikutnya
                $pengajuan->update([
                    'current_step' => $currentStep + 1
                ]);

                // Update progress approval untuk step berikutnya
                $nextProgress = ProgressApproval::where('pengajuan_id', $pengajuan->id)
                    ->where('urutan', $currentStep + 1)
                    ->first();

                if ($nextProgress) {
                    $nextProgress->update(['status' => 'proses']);
                    $nextProgress->assignApprover();
                }
                
                Log::info("Moving to next step: " . ($currentStep + 1));
            }

            DB::commit();

            // Refresh pengajuan untuk mendapatkan data terbaru
            $pengajuan = $pengajuan->fresh();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil disetujui' . ($pengajuan->settlement ? ' dan settlement otomatis dibuat' : ''),
                'data' => [
                    'status' => $pengajuan->status_pengajuan,
                    'current_step' => $pengajuan->current_step,
                    'settlement_created' => $pengajuan->settlement ? true : false,
                    'settlement_id' => $pengajuan->settlement_id
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Approval failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function generateWorkflowStepsFromDatabase($kategoriPengajuanId, $requester)
    {
        $steps = collect();
        
        // Ambil semua flow approval yang aktif untuk kategori ini, diurutkan berdasarkan urutan
        $flowApprovals = FlowApproval::where('kategori_pengajuan_id', $kategoriPengajuanId)
            ->where('status', 'aktif')
            ->orderBy('urutan')
            ->get();
        
        if ($flowApprovals->isEmpty()) {
            // Jika tidak ada flow approval di database, buat default workflow
            return $this->generateDefaultWorkflow($kategoriPengajuanId, $requester);
        }
        
        foreach ($flowApprovals as $flow) {
            // Skip jika requester adalah approver untuk step ini
            if ($flow->approver_id && $flow->approver_id == $requester->id) {
                continue;
            }
            
            // Skip jika requester memiliki level yang sama atau lebih tinggi dari approver
            if ($this->shouldSkipStep($flow, $requester)) {
                continue;
            }
            
            $steps->push([
                'flow_approval_id' => $flow->id,
                'requester_id' => $requester->id,
                'approver_id' => $flow->approver_id,
                'step_name' => $flow->nama_step,
                'urutan' => $flow->urutan,
                'deskripsi' => $flow->deskripsi
            ]);
        }
        
        // Re-order urutan steps setelah ada yang di-skip
        $reorderedSteps = collect();
        $newOrder = 1;
        
        foreach ($steps as $step) {
            $step['urutan'] = $newOrder++;
            $reorderedSteps->push($step);
        }
        
        return $reorderedSteps;
    }
    
    /**
     * Cek apakah step harus di-skip berdasarkan hirarki
     */
    private function shouldSkipStep($flowApproval, $requester)
    {
        if (!$flowApproval->approver_id) {
            return false;
        }
        
        $approver = Karyawan::find($flowApproval->approver_id);
        if (!$approver) {
            return false;
        }
        
        // Jika approver dan requester di department yang sama
        if ($approver->department_id == $requester->department_id) {
            $requesterLevel = $requester->roleLevel;
            $approverLevel = $approver->roleLevel;
            
            if (!$requesterLevel || !$approverLevel) {
                return false;
            }
            
            // Skip jika requester memiliki level yang sama atau lebih tinggi
            // Asumsi: level yang lebih tinggi memiliki ID yang lebih besar
            if ($requesterLevel->level >= $approverLevel->level) {
                return true;
            }
        }
        
        // Logic khusus untuk cross-department
        // Contoh: GM Sales tidak perlu approval dari Finance level rendah
        if ($requester->roleLevel && $requester->roleLevel->nama == 'General Manager') {
            if ($approver->roleLevel && in_array($approver->roleLevel->nama, ['Finance 1', 'Staff'])) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Generate default workflow jika tidak ada data di FlowApproval
     */
    private function generateDefaultWorkflow($kategoriPengajuanId, $requester)
    {
        $steps = collect();
        $stepOrder = 1;
        
        // Default workflow: Atasan langsung -> Department Head -> Finance -> Direksi
        
        // 1. Atasan langsung (jika ada dan berbeda dengan requester)
        if ($requester->atasan && $requester->atasan->id != $requester->id) {
            $steps->push([
                'flow_approval_id' => null,
                'requester_id' => $requester->id,
                'approver_id' => $requester->atasan->id,
                'step_name' => 'Approval Atasan Langsung',
                'urutan' => $stepOrder++,
                'deskripsi' => 'Approval dari atasan langsung'
            ]);
        }
        
        // 2. Department Head (jika requester bukan head department)
        $departmentHead = Karyawan::where('department_id', $requester->department_id)
            ->whereHas('roleLevel', function($q) {
                $q->where('nama', 'like', '%General Manager%')
                  ->orWhere('nama', 'like', '%Head%');
            })
            ->where('id', '!=', $requester->id)
            ->first();
        
        if ($departmentHead) {
            $steps->push([
                'flow_approval_id' => null,
                'requester_id' => $requester->id,
                'approver_id' => $departmentHead->id,
                'step_name' => 'Approval Head Department',
                'urutan' => $stepOrder++,
                'deskripsi' => 'Approval dari kepala departemen'
            ]);
        }
        
        // 3. Finance (jika pengajuan memerlukan review finance)
        $financeApprover = Karyawan::whereHas('department', function($q) {
                $q->where('nama', 'like', '%Finance%');
            })
            ->whereHas('roleLevel', function($q) {
                $q->where('nama', 'like', '%Finance%');
            })
            ->where('status', 'aktif')
            ->orderBy('role_level_id', 'desc')
            ->first();
        
        if ($financeApprover) {
            $steps->push([
                'flow_approval_id' => null,
                'requester_id' => $requester->id,
                'approver_id' => $financeApprover->id,
                'step_name' => 'Review Finance',
                'urutan' => $stepOrder++,
                'deskripsi' => 'Review dari bagian finance'
            ]);
        }
        
        // 4. Direksi (untuk pengajuan dengan nominal besar atau kategori tertentu)
        $kategori = KategoriPengajuan::find($kategoriPengajuanId);
        if ($kategori && $this->requiresDireksiApproval($kategori)) {
            $direksi = Karyawan::whereHas('roleLevel', function($q) {
                    $q->where('nama', 'like', '%Direksi%');
                })
                ->where('status', 'aktif')
                ->first();
            
            if ($direksi) {
                $steps->push([
                    'flow_approval_id' => null,
                    'requester_id' => $requester->id,
                    'approver_id' => $direksi->id,
                    'step_name' => 'Final Approval Direksi',
                    'urutan' => $stepOrder++,
                    'deskripsi' => 'Final approval dari direksi'
                ]);
            }
        }
        
        return $steps;
    }

    /**
     * Cek apakah kategori memerlukan approval direksi
     */
    private function requiresDireksiApproval($kategori)
    {
        // Logic untuk menentukan kapan perlu approval direksi
        // Bisa berdasarkan kategori atau nominal
        $kategoriPerluDireksi = ['Investasi', 'Pembelian Aset', 'Kontrak Besar'];
        
        return in_array($kategori->nama, $kategoriPerluDireksi) || $kategori->settlement;
    }
    
    /**
     * Enhanced findApprovers method dengan additional logic
     */
    private function findApprovers($flowApproval, $requester)
    {
        $query = Karyawan::query();
    
        // Filter berdasarkan department jika diperlukan
        if ($flowApproval->department_id) {
            $query->where('department_id', $flowApproval->department_id);
        }
    
        // Filter berdasarkan role level jika diperlukan
        if ($flowApproval->role_level_id) {
            $query->where('role_level_id', $flowApproval->role_level_id);
        }
    
        // Filter hanya karyawan aktif
        $query->aktif();
        
        // Exclude requester sendiri dari list approver
        $query->where('id', '!=', $requester->id);
    
        // Logic khusus berdasarkan tipe flow
        switch ($flowApproval->type) {
            case 'atasan_langsung':
                // Cari atasan langsung yang masih aktif
                $atasanLangsung = $requester->atasan;
                return $atasanLangsung && $atasanLangsung->status == 'aktif' 
                    ? collect([$atasanLangsung]) 
                    : collect([]);
                    
            case 'department_head':
                // Cari head department (yang tidak punya atasan di department yang sama)
                return $query->where('atasan_id', null)
                    ->orWhereHas('atasan', function($q) use ($flowApproval) {
                        $q->where('department_id', '!=', $flowApproval->department_id);
                    })
                    ->get();
                    
            case 'finance':
                // Khusus untuk finance, ambil berdasarkan hierarchy
                return $query->orderBy('role_level_id', 'desc')->get();
                    
            case 'direksi':
                // Untuk direksi, biasanya hanya ada satu atau beberapa
                return $query->get();
                    
            default:
                return $query->get();
        }
    }
    
    private function isHighLevelEmployee($karyawan)
    {
        $highLevelRoles = ['General Manager', 'Direksi', 'Finance 3'];
        
        return $karyawan->roleLevel && 
               in_array($karyawan->roleLevel->nama, $highLevelRoles);
    }
    
    /**
     * Method helper untuk mendapatkan minimum approval level yang diperlukan
     */
    private function getMinimumApprovalLevel($karyawan, $kategoriPengajuanId)
    {
        // Logic untuk menentukan level minimum approval berdasarkan:
        // 1. Level requester
        // 2. Kategori pengajuan
        // 3. Nominal (jika ada)
        
        if ($karyawan->roleLevel->nama == 'General Manager') {
            // GM minimal perlu approval dari Finance 2 atau Direksi
            return ['Finance 1','Finance 2', 'Finance 3', 'Direksi'];
        }
        
        if ($karyawan->roleLevel->nama == 'Supervisor') {
            // Supervisor minimal perlu approval dari GM department sendiri
            return ['General Manager', 'Finance 2', 'Finance 3', 'Direksi'];
        }
        
        // Staff perlu approval dari semua level di atasnya
        return ['Supervisor', 'General Manager', 'Finance 1', 'Finance 2', 'Finance 3', 'Direksi'];
    }

    private function generateDepartmentSteps($kategoriPengajuanId, Karyawan $requester, $startOrder)
    {
        $steps = collect();
        $stepOrder = $startOrder;

        // Ambil hierarchy chain dari requester ke atas
        $hierarchyChain = $requester->getHierarchyChain();
        
        // Skip requester sendiri, mulai dari atasan langsung
        $approvers = $hierarchyChain->skip(1);

        foreach ($approvers as $approver) {
            // Cek apakah ada flow approval yang sudah didefinisikan
            $flowApproval = FlowApproval::where('kategori_pengajuan_id', $kategoriPengajuanId)
                ->where('department_id', $approver->department_id)
                ->where('role_level_id', $approver->role_level_id)
                ->aktif()
                ->first();

            $steps->push([
                'flow_approval_id' => $flowApproval ? $flowApproval->id : null,
                'department_id' => $approver->department_id,
                'role_level_id' => $approver->role_level_id,
                'step_name' => $flowApproval ? 
                    $flowApproval->nama_step : 
                    $approver->roleLevel->nama . ' - ' . $approver->department->nama,
                'urutan' => $stepOrder++,
                'type' => 'department'
            ]);

            // Berhenti jika sudah sampai head department
            if ($approver->isDepartmentHead()) {
                break;
            }
        }

        return $steps;
    }

    private function generateFinanceSteps($kategoriPengajuanId, $startOrder)
    {
        $steps = collect();
        $stepOrder = $startOrder;
        
        $financeDept = Department::getFinanceDepartment();
        if (!$financeDept) {
            throw new \Exception('Department Finance tidak ditemukan');
        }

        // Level 1: Finance Staff
        $financeStaffRole = RoleLevel::where('department_id', $financeDept->id)
            ->where('level', 1)
            ->aktif()
            ->first();

        if ($financeStaffRole) {
            $steps->push([
                'flow_approval_id' => null,
                'department_id' => $financeDept->id,
                'role_level_id' => $financeStaffRole->id,
                'step_name' => 'Finance Staff Review',
                'urutan' => $stepOrder++,
                'type' => 'finance'
            ]);
        }

        // Level 2: Finance Supervisor
        $financeSupervisorRole = RoleLevel::where('department_id', $financeDept->id)
            ->where('level', 2)
            ->aktif()
            ->first();

        if ($financeSupervisorRole) {
            $steps->push([
                'flow_approval_id' => null,
                'department_id' => $financeDept->id,
                'role_level_id' => $financeSupervisorRole->id,
                'step_name' => 'Finance Supervisor Review',
                'urutan' => $stepOrder++,
                'type' => 'finance'
            ]);
        }

        // Level 3: Finance Manager
        $financeManagerRole = RoleLevel::where('department_id', $financeDept->id)
            ->where('level', 3)
            ->aktif()
            ->first();

        if ($financeManagerRole) {
            $steps->push([
                'flow_approval_id' => null,
                'department_id' => $financeDept->id,
                'role_level_id' => $financeManagerRole->id,
                'step_name' => 'Finance Manager Approval',
                'urutan' => $stepOrder++,
                'type' => 'finance'
            ]);
        }

        return $steps;
    }

    private function generateDireksiSteps($kategoriPengajuanId, $startOrder)
    {
        $steps = collect();
        
        $direksiDept = Department::getDireksiDepartment();
        if (!$direksiDept) {
            throw new \Exception('Department Direksi tidak ditemukan');
        }

        $direksiRole = RoleLevel::where('department_id', $direksiDept->id)
            ->aktif()
            ->first();

        if ($direksiRole) {
            $steps->push([
                'flow_approval_id' => null,
                'department_id' => $direksiDept->id,
                'role_level_id' => $direksiRole->id,
                'step_name' => 'Direksi Final Approval',
                'urutan' => $startOrder,
                'type' => 'direksi'
            ]);
        }

        return $steps;
    }

    private function canApprove(ProgressApproval $progressApproval, Karyawan $approver)
    {
        // Jika sudah di-assign specific approver
        if ($progressApproval->approver_id) {
            return $progressApproval->approver_id == $approver->id;
        }

        // Jika belum di-assign, cek berdasarkan department dan role level
        return $approver->department_id == $progressApproval->department_id &&
               $approver->role_level_id == $progressApproval->role_level_id;
    }

    private function createSettlement(Pengajuan $pengajuan)
    {
        try {
            Log::info("Starting settlement creation for pengajuan ID: {$pengajuan->id}");
            
            $tahun = date('Y');
            $bulan = date('m');
            
            $lastSettlement = Settlement::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)
                ->count();
            
            $sequence = str_pad($lastSettlement + 1, 4, '0', STR_PAD_LEFT);
            $nomorSettlement = 'STL/' . $tahun . $bulan . '/' . $sequence;

            Log::info("Generated settlement number: {$nomorSettlement}");

            $settlement = Settlement::create([
                'pengajuan_id' => $pengajuan->id,
                'nomor_settlement' => $nomorSettlement,
                'tanggal_settlement' => now(),
                'total_actual' => 0, // Akan diisi user nanti
                'selisih' => $pengajuan->nominal_pengajuan, // Awalnya selisih = nominal pengajuan
                'status_settlement' => 'pending', // User belum mengisi data actual
                'catatan_settlement' => 'Settlement otomatis dibuat dari pengajuan: ' . $pengajuan->nomor_pengajuan . '. Silakan lengkapi data actual cost.',
                'file_bukti' => null,
                'current_step' => 1,
                'total_step' => 1
            ]);

            Log::info("Settlement created with ID: {$settlement->id}");
            
            return $settlement;

        } catch (\Exception $e) {
            Log::error("Error creating settlement: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            throw new \Exception('Gagal membuat settlement: ' . $e->getMessage());
        }
    }
    
    public function debugSettlement($pengajuanId)
    {
        $pengajuan = Pengajuan::with('settlement')->findOrFail($pengajuanId);
        
        $debug = [
            'pengajuan_id' => $pengajuan->id,
            'nomor_pengajuan' => $pengajuan->nomor_pengajuan,
            'status_pengajuan' => $pengajuan->status_pengajuan,
            'current_step' => $pengajuan->current_step,
            'total_step' => $pengajuan->total_step,
            'is_settlement_required' => $pengajuan->is_settlement_required,
            'settlement_id' => $pengajuan->settlement_id,
            'has_settlement' => $pengajuan->settlement ? true : false,
            'can_create_settlement' => $pengajuan->canCreateSettlement(),
            'is_fully_approved' => $pengajuan->isFullyApproved()
        ];
        
        if ($pengajuan->settlement) {
            $debug['settlement_data'] = [
                'id' => $pengajuan->settlement->id,
                'nomor_settlement' => $pengajuan->settlement->nomor_settlement,
                'status_settlement' => $pengajuan->settlement->status_settlement,
                'total_actual' => $pengajuan->settlement->total_actual,
                'selisih' => $pengajuan->settlement->selisih
            ];
        }
        
        return response()->json($debug);
    }
    
    public function forceCreateSettlement($pengajuanId)
    {
        try {
            DB::beginTransaction();
            
            $pengajuan = Pengajuan::findOrFail($pengajuanId);
            
            // Check jika settlement sudah ada
            if ($pengajuan->settlement_id) {
                throw new \Exception('Settlement sudah ada untuk pengajuan ini');
            }
            
            // Force create settlement
            $settlement = $this->createSettlement($pengajuan);
            $pengajuan->update(['settlement_id' => $settlement->id]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Settlement berhasil dibuat',
                'data' => [
                    'settlement_id' => $settlement->id,
                    'nomor_settlement' => $settlement->nomor_settlement
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function generateNomorPengajuanSafe($kategoriId)
    {
        $kategori = KategoriPengajuan::find($kategoriId);
        if (!$kategori) {
            throw new \Exception('Kategori pengajuan tidak ditemukan');
        }
    
        $tahun = date('Y');
        $bulan = date('m');
        
        // Loop sampai dapat nomor yang unik
        $maxAttempts = 10;
        $attempt = 0;
        
        do {
            $attempt++;
            
            // Ambil sequence terakhir + tambahan microsecond untuk uniqueness
            $lastSequence = Pengajuan::where('kategori_pengajuan_id', $kategoriId)
                ->whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)
                ->where('nomor_pengajuan', 'LIKE', $kategori->kode . '/' . $tahun . $bulan . '/%')
                ->max(DB::raw('CAST(SUBSTRING_INDEX(nomor_pengajuan, "/", -1) AS UNSIGNED)'));
            
            // Tambahkan microsecond untuk menghindari duplicate
            $sequence = str_pad(($lastSequence ?? 0) + $attempt, 4, '0', STR_PAD_LEFT);
            $nomorPengajuan = $kategori->kode . '/' . $tahun . $bulan . '/' . $sequence;
            
            // Cek apakah nomor sudah ada
            $exists = Pengajuan::where('nomor_pengajuan', $nomorPengajuan)->exists();
            
            if (!$exists) {
                return $nomorPengajuan;
            }
            
            // Jika masih duplicate, tunggu sebentar
            usleep(100000); // 0.1 detik
            
        } while ($attempt < $maxAttempts);
        
        // Fallback: gunakan timestamp jika semua attempt gagal
        $timestamp = substr(microtime(true) * 10000, -4);
        return $kategori->kode . '/' . $tahun . $bulan . '/' . $timestamp;
    }

    /**
     * Simpan sebagai draft
     */
    public function saveDraft(Request $request)
    {
        $request->merge(['submit_action' => 'draft']);
        return $this->store($request);
    }

    /**
     * Submit pengajuan
     */
    public function submit(Request $request)
    {
        $request->merge(['submit_action' => 'submit']);
        return $this->store($request);
    }
    
    
    public function getBuktiDetail($id)
    {
        try {
            // Cek apakah TransactionRequest exists
            $transactionRequest = TransactionRequest::find($id);
            
            if (!$transactionRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction request tidak ditemukan'
                ], 404);
            }
    
            // Load relasi dengan pengecekan yang lebih aman
            $transactionRequest->load([
                'processedBy' => function($query) {
                    $query->select('id', 'nama');
                },
                'pengajuan' => function($query) {
                    $query->select('id', 'nomor_pengajuan');
                }
            ]);
            
            // Format data untuk response
            $responseData = [
                'id' => $transactionRequest->id,
                'status' => $transactionRequest->status,
                'catatan_finance' => $transactionRequest->catatan_finance,
                'bukti_transfer' => $transactionRequest->bukti_transfer,
                'tanggal_transfer' => $transactionRequest->tanggal_transfer,
                'updated_at' => $transactionRequest->updated_at,
                'processed_by' => $transactionRequest->processedBy ? [
                    'id' => $transactionRequest->processedBy->id,
                    'nama' => $transactionRequest->processedBy->nama
                ] : null,
                'pengajuan' => $transactionRequest->pengajuan ? [
                    'id' => $transactionRequest->pengajuan->id,
                    'nomor_pengajuan' => $transactionRequest->pengajuan->nomor_pengajuan
                ] : null
            ];
            
            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);
            
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Error in getBuktiDetail: ' . $e->getMessage(), [
                'transaction_request_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function downloadBukti($id)
    {
        try {
            $transactionRequest = TransactionRequest::findOrFail($id);
            
            if (!$transactionRequest->bukti_transfer) {
                return response()->json([
                    'success' => false,
                    'message' => 'File bukti transfer tidak ditemukan'
                ], 404);
            }
            
            // PERBAIKAN: Gunakan disk yang sama dengan saat upload (custom_public)
            if (!Storage::disk('custom_public')->exists($transactionRequest->bukti_transfer)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan di server'
                ], 404);
            }
            
            // Dapatkan path lengkap menggunakan custom_public disk
            $filePath = Storage::disk('custom_public')->path($transactionRequest->bukti_transfer);
            
            // Dapatkan nama file asli untuk download
            $originalFileName = basename($transactionRequest->bukti_transfer);
            
            return response()->download($filePath, $originalFileName);
            
        } catch (\Exception $e) {
            \Log::error('Download bukti transfer error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh file: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getBuktiUrl($id)
    {
        try {
            $transactionRequest = TransactionRequest::findOrFail($id);
            
            if (!$transactionRequest->bukti_transfer) {
                return response()->json([
                    'success' => false,
                    'message' => 'File bukti transfer tidak ditemukan'
                ], 404);
            }
            
            // PERBAIKAN: Gunakan disk yang sama dengan saat upload
            if (!Storage::disk('custom_public')->exists($transactionRequest->bukti_transfer)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan di server'
                ], 404);
            }
            
            // Dapatkan URL menggunakan custom_public disk
            $fileUrl = Storage::disk('custom_public')->url($transactionRequest->bukti_transfer);
            
            return response()->json([
                'success' => true,
                'url' => $fileUrl
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Get bukti URL error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan URL file'
            ], 500);
        }
    }
    
    // Method untuk preview image langsung
    public function previewBukti($id)
    {
        try {
            $transactionRequest = TransactionRequest::findOrFail($id);
            
            if (!$transactionRequest->bukti_transfer) {
                abort(404, 'File tidak ditemukan');
            }
            
            if (!Storage::disk('custom_public')->exists($transactionRequest->bukti_transfer)) {
                abort(404, 'File tidak ditemukan di server');
            }
            
            $filePath = Storage::disk('custom_public')->path($transactionRequest->bukti_transfer);
            $mimeType = Storage::disk('custom_public')->mimeType($transactionRequest->bukti_transfer);
            
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=31536000',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Preview bukti transfer error: ' . $e->getMessage());
            abort(404, 'File tidak dapat dimuat');
        }
    }
    
    
    
}