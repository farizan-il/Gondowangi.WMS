<?php

namespace App\Http\Controllers\Gondowangi\Kandidat\AdminKandidat;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\CareerPosition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LolosController extends Controller
{
    /**
     * Display the main page for managing passed candidates
     */
    public function index()
    {
        return view('Gondowangi.Kandidat.Admin.lolos');
    }

    /**
     * Get data for candidates who passed to the next stage
     */
    public function getLolosData(): JsonResponse
    {
        try {
            // Get candidates with status 'Lanjut', 'Diterima', or 'Cocok'
            $kandidats = Karyawan::with(['posisilamaran', 'credentials'])
                ->whereIn('status', ['Lanjut', 'Diterima', 'Cocok'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Transform data for frontend
            $transformedKandidats = $kandidats->map(function ($kandidat) {
                return [
                    'id' => $kandidat->id,
                    'nama' => $kandidat->nama,
                    'email' => $kandidat->email,
                    'kota_domisili' => $kandidat->kota_domisili,
                    'tanggal_lahir' => $kandidat->tanggal_lahir,
                    'no_telepon' => $kandidat->no_telepon,
                    'posisi_dilamar_id' => $kandidat->posisi_dilamar_id,
                    'status' => $kandidat->status,
                    'gaji_diharapkan' => $kandidat->gaji_diharapkan,
                    'jabatan_diminati' => $kandidat->jabatan_diminati,
                    'informasi_tambahan' => $kandidat->informasi_tambahan,
                    'created_at' => $kandidat->created_at,
                    'posisilamaran' => $kandidat->posisilamaran ? [
                        'id' => $kandidat->posisilamaran->id,
                        'position_title' => $kandidat->posisilamaran->position_title,
                        'department' => $kandidat->posisilamaran->department,
                    ] : null,
                ];
            });

            // Get statistics
            $statistics = [
                'total_lolos' => $kandidats->where('status', 'Lanjut')->count(),
                'total_diterima' => $kandidats->where('status', 'Diterima')->count(),
                'total_cocok' => $kandidats->where('status', 'Cocok')->count(),
                'total_positions' => CareerPosition::count(),
            ];

            return response()->json([
                'success' => true,
                'kandidats' => $transformedKandidats,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching lolos data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kandidat lolos'
            ], 500);
        }
    }

    /**
     * Get filter options (positions)
     */
    public function getFilterOptions(): JsonResponse
    {
        try {
            $positions = CareerPosition::select('id', 'position_title', 'department')
                ->whereHas('karyawan', function ($query) {
                    $query->whereIn('status', ['Lanjut', 'Diterima', 'Cocok']);
                })
                ->get();

            return response()->json([
                'success' => true,
                'positions' => $positions
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching filter options: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data filter'
            ], 500);
        }
    }

    /**
     * Get candidates by position
     */
    public function getKandidatsByPosition(Request $request): JsonResponse
    {
        try {
            $positionId = $request->get('position_id');
            $status = $request->get('status');

            $query = Karyawan::with(['posisilamaran', 'credentials'])
                ->whereIn('status', ['Lanjut', 'Diterima', 'Cocok']);

            // Filter by position if specified
            if ($positionId && $positionId !== 'all') {
                $query->where('posisi_dilamar_id', $positionId);
            }

            // Filter by status if specified
            if ($status) {
                $query->where('status', $status);
            }

            $kandidats = $query->orderBy('created_at', 'desc')->get();

            // Transform data
            $transformedKandidats = $kandidats->map(function ($kandidat) {
                return [
                    'id' => $kandidat->id,
                    'nama' => $kandidat->nama,
                    'email' => $kandidat->email,
                    'kota_domisili' => $kandidat->kota_domisili,
                    'tanggal_lahir' => $kandidat->tanggal_lahir,
                    'no_telepon' => $kandidat->no_telepon,
                    'posisi_dilamar_id' => $kandidat->posisi_dilamar_id,
                    'status' => $kandidat->status,
                    'gaji_diharapkan' => $kandidat->gaji_diharapkan,
                    'jabatan_diminati' => $kandidat->jabatan_diminati,
                    'informasi_tambahan' => $kandidat->informasi_tambahan,
                    'created_at' => $kandidat->created_at,
                    'posisilamaran' => $kandidat->posisilamaran ? [
                        'id' => $kandidat->posisilamaran->id,
                        'position_title' => $kandidat->posisilamaran->position_title,
                        'department' => $kandidat->posisilamaran->department,
                    ] : null,
                ];
            });

            return response()->json([
                'success' => true,
                'kandidats' => $transformedKandidats
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching candidates by position: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kandidat'
            ], 500);
        }
    }

    /**
     * Update candidate status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|in:Lanjut,Diterima,Cocok',
                'catatan' => 'nullable|string|max:1000'
            ]);

            $kandidat = Karyawan::findOrFail($id);
            $oldStatus = $kandidat->status;
            $newStatus = $request->status;

            // Update status
            $kandidat->status = $newStatus;
            
            // If there's additional information field for notes, update it
            if ($request->catatan) {
                $currentInfo = $kandidat->informasi_tambahan ?? '';
                $dateNow = now()->format('d/m/Y H:i');
                $newNote = "\n[{$dateNow}] Status diubah dari '{$oldStatus}' ke '{$newStatus}': {$request->catatan}";
                $kandidat->informasi_tambahan = $currentInfo . $newNote;
            }

            $kandidat->save();

            // Log the activity
            Log::info("Candidate status updated", [
                'kandidat_id' => $kandidat->id,
                'kandidat_name' => $kandidat->nama,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'updated_by' => auth()->user()->fullName ?? 'System',
                'catatan' => $request->catatan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status kandidat berhasil diperbarui',
                'data' => [
                    'id' => $kandidat->id,
                    'status' => $kandidat->status,
                    'updated_at' => $kandidat->updated_at
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kandidat tidak ditemukan'
            ], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error updating candidate status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status kandidat'
            ], 500);
        }
    }

    /**
     * Get candidate detail
     */
    public function getKandidatDetail($id): JsonResponse
    {
        try {
            $kandidat = Karyawan::with(['posisilamaran', 'credentials'])
                ->findOrFail($id);

            $data = [
                'id' => $kandidat->id,
                'nama' => $kandidat->nama,
                'email' => $kandidat->email,
                'kota_domisili' => $kandidat->kota_domisili,
                'tanggal_lahir' => $kandidat->tanggal_lahir,
                'no_telepon' => $kandidat->no_telepon,
                'cv' => $kandidat->cv,
                'foto' => $kandidat->foto,
                'pendidikan_formal' => $kandidat->pendidikan_formal,
                'pengalaman_kerja' => $kandidat->pengalaman_kerja,
                'gaji_terakhir' => $kandidat->gaji_terakhir,
                'tunjangan_terakhir' => $kandidat->tunjangan_terakhir,
                'fasilitas_terakhir' => $kandidat->fasilitas_terakhir,
                'fasilitas_lain' => $kandidat->fasilitas_lain,
                'jabatan_diminati' => $kandidat->jabatan_diminati,
                'gaji_diharapkan' => $kandidat->gaji_diharapkan,
                'tunjangan_diharapkan' => $kandidat->tunjangan_diharapkan,
                'fasilitas_diharapkan' => $kandidat->fasilitas_diharapkan,
                'jaminan_diharapkan' => $kandidat->jaminan_diharapkan,
                'lain_diharapkan' => $kandidat->lain_diharapkan,
                'informasi_tambahan' => $kandidat->informasi_tambahan,
                'status' => $kandidat->status,
                'created_at' => $kandidat->created_at,
                'posisilamaran' => $kandidat->posisilamaran ? [
                    'id' => $kandidat->posisilamaran->id,
                    'position_title' => $kandidat->posisilamaran->position_title,
                    'department' => $kandidat->posisilamaran->department,
                    'job_type' => $kandidat->posisilamaran->job_type,
                    'location' => $kandidat->posisilamaran->location,
                ] : null,
                'credentials' => $kandidat->credentials ? [
                    'fullName' => $kandidat->credentials->fullName,
                    'email' => $kandidat->credentials->email,
                ] : null,
            ];

            return response()->json([
                'success' => true,
                'kandidat' => $data
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kandidat tidak ditemukan'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error fetching candidate detail: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail kandidat'
            ], 500);
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $statistics = [
                'total_lolos' => Karyawan::where('status', 'Lanjut')->count(),
                'total_diterima' => Karyawan::where('status', 'Diterima')->count(),
                'total_cocok' => Karyawan::where('status', 'Cocok')->count(),
                'total_positions' => CareerPosition::count(),
                'by_position' => DB::table('karyawan')
                    ->join('career_positions', 'karyawan.posisi_dilamar_id', '=', 'career_positions.id')
                    ->whereIn('karyawan.status', ['Lanjut', 'Diterima', 'Cocok'])
                    ->select(
                        'career_positions.position_title',
                        'career_positions.id as position_id',
                        DB::raw('COUNT(karyawan.id) as total'),
                        DB::raw('SUM(CASE WHEN karyawan.status = "Lanjut" THEN 1 ELSE 0 END) as lolos'),
                        DB::raw('SUM(CASE WHEN karyawan.status = "Diterima" THEN 1 ELSE 0 END) as diterima'),
                        DB::raw('SUM(CASE WHEN karyawan.status = "Cocok" THEN 1 ELSE 0 END) as cocok')
                    )
                    ->groupBy('career_positions.id', 'career_positions.position_title')
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching statistics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data statistik'
            ], 500);
        }
    }

    /**
     * Bulk update status for multiple candidates
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'kandidat_ids' => 'required|array',
                'kandidat_ids.*' => 'exists:karyawan,id',
                'status' => 'required|in:Lanjut,Diterima,Cocok',
                'catatan' => 'nullable|string|max:1000'
            ]);

            DB::beginTransaction();

            $updatedCount = 0;
            $dateNow = now()->format('d/m/Y H:i');
            
            foreach ($request->kandidat_ids as $id) {
                $kandidat = Karyawan::find($id);
                if ($kandidat && in_array($kandidat->status, ['Lanjut', 'Diterima', 'Cocok'])) {
                    $oldStatus = $kandidat->status;
                    $kandidat->status = $request->status;
                    
                    if ($request->catatan) {
                        $currentInfo = $kandidat->informasi_tambahan ?? '';
                        $newNote = "\n[{$dateNow}] Status diubah massal dari '{$oldStatus}' ke '{$request->status}': {$request->catatan}";
                        $kandidat->informasi_tambahan = $currentInfo . $newNote;
                    }
                    
                    $kandidat->save();
                    $updatedCount++;
                }
            }

            DB::commit();

            Log::info("Bulk candidate status update", [
                'updated_count' => $updatedCount,
                'new_status' => $request->status,
                'updated_by' => auth()->user()->fullName ?? 'System',
                'catatan' => $request->catatan
            ]);

            return response()->json([
                'success' => true,
                'message' => "Status {$updatedCount} kandidat berhasil diperbarui",
                'updated_count' => $updatedCount
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
            Log::error('Error bulk updating candidate status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status kandidat secara massal'
            ], 500);
        }
    }

    /**
     * Export candidates data
     */
    public function exportData(Request $request)
    {
        try {
            $query = Karyawan::with(['posisilamaran'])
                ->whereIn('status', ['Lanjut', 'Diterima', 'Cocok']);

            // Apply filters
            if ($request->position_id && $request->position_id !== 'all') {
                $query->where('posisi_dilamar_id', $request->position_id);
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $kandidats = $query->orderBy('created_at', 'desc')->get();

            // Prepare CSV data
            $csvData = [];
            $csvData[] = [
                'No',
                'Nama',
                'Email',
                'Posisi Dilamar',
                'Department',
                'Kota Domisili',
                'No. Telepon',
                'Status',
                'Gaji Diharapkan',
                'Jabatan Diminati',
                'Tanggal Daftar'
            ];

            foreach ($kandidats as $index => $kandidat) {
                $csvData[] = [
                    $index + 1,
                    $kandidat->nama,
                    $kandidat->email,
                    $kandidat->posisilamaran->position_title ?? 'N/A',
                    $kandidat->posisilamaran->department ?? 'N/A',
                    $kandidat->kota_domisili,
                    $kandidat->no_telepon,
                    $kandidat->status,
                    $kandidat->gaji_diharapkan ? 'Rp ' . number_format($kandidat->gaji_diharapkan, 0, ',', '.') : 'N/A',
                    $kandidat->jabatan_diminati ?? 'N/A',
                    $kandidat->created_at->format('d/m/Y H:i')
                ];
            }

            // Generate CSV
            $filename = 'kandidat_lolos_' . date('Y-m-d_H-i-s') . '.csv';
            $handle = fopen('php://temp', 'r+');
            
            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            
            rewind($handle);
            $csvContent = stream_get_contents($handle);
            fclose($handle);

            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            Log::error('Error exporting data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor data'
            ], 500);
        }
    }
}