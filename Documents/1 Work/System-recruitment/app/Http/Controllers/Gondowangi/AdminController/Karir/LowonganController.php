<?php

namespace App\Http\Controllers\Gondowangi\AdminController\Karir;

use App\Http\Controllers\Controller;
use App\Models\CareerPosition;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LowonganController extends Controller
{
    public function index()
    {
        // Ambil semua data lowongan dengan jumlah pelamar
        $lowongan = CareerPosition::withCount(['applications', 'karyawan'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung statistik
        $totalLowongan = CareerPosition::count();
        $aktif = CareerPosition::where('status', 'active')->count();
        $nonaktif = CareerPosition::where('status', 'inactive')->count();
        $totalPelamar = JobApplication::count();

        // Ambil departemen unik untuk filter
        $departments = CareerPosition::select('department')
            ->distinct()
            ->pluck('department')
            ->filter();

        return view('Gondowangi.Admin.Karir.Lowongan', [
            'title' => 'Karir',
            'lowongan' => $lowongan,
            'stats' => [
                'total_lowongan' => $totalLowongan,
                'aktif' => $aktif,
                'nonaktif' => $nonaktif,
                'total_pelamar' => $totalPelamar
            ],
            'departments' => $departments
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'position_title' => 'required|string|max:255',
            'department' => 'required|string',
            'job_type' => 'required|string',
            'location' => 'required|string',
            'deadline' => 'required|date',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'salary_range' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:9048',
            'status' => 'required|in:open,closed,draf'
        ]);
    
        $data = $request->all();
        $data['posted_date'] = now();
    
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/karir'), $imageName);
            $data['image_url'] = 'assets/karir/' . $imageName; // simpan path di database
        }
    
        CareerPosition::create($data);
    
        return redirect()->back()->with('success', 'Lowongan berhasil ditambahkan!');
    }


    public function show($id)
    {
        $lowongan = CareerPosition::withCount('applications')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $lowongan
        ]);
    }

    public function update(Request $request, $id) 
    {
        try {
            $lowongan = CareerPosition::findOrFail($id);

            $request->validate([
                'position_title' => 'required|string|max:255',
                'department' => 'required|string',
                'job_type' => 'required|string',
                'location' => 'required|string',
                'deadline' => 'required|date',
                'description' => 'required|string',
                'requirements' => 'required|string',
                'salary_range' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'status' => 'required|in:open,close,draf',
                'remove_image' => 'nullable|in:0,1'
            ]);

            // Ambil data yang akan diupdate, kecuali field yang tidak perlu
            $data = $request->except(['_token', '_method', 'image', 'remove_image', 'job_id']);

            // Handle image removal
            if ($request->remove_image == '1') {
                if ($lowongan->image_url) {
                    $this->deleteImage($lowongan->image_url);
                }
                $data['image_url'] = null;
            }

            // Handle new image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($lowongan->image_url) {
                    $this->deleteImage($lowongan->image_url);
                }
                
                // Upload new image
                $data['image_url'] = $this->uploadImage($request->file('image'));
            }

            // Update data
            $lowongan->update($data);

            // Return JSON response for AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lowongan berhasil diperbarui!',
                    'data' => $lowongan->fresh()
                ]);
            }

            return redirect()->back()->with('success', 'Lowongan berhasil diperbarui!');
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }
    
    private function uploadImage($image)
    {
        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('assets/karir'), $imageName);
        return 'assets/karir/' . $imageName;
    }

    /**
     * Delete image dari storage
     */
    private function deleteImage($imagePath)
    {
        if ($imagePath && File::exists(public_path($imagePath))) {
            File::delete(public_path($imagePath));
        }
    }

    public function destroy($id)
    {
        $lowongan = CareerPosition::findOrFail($id);
        
        // Delete image if exists
        if ($lowongan->image_url && file_exists(public_path($lowongan->image_url))) {
            unlink(public_path($lowongan->image_url));
        }
        
        $lowongan->delete();

        return redirect()->back()->with('success', 'Lowongan berhasil dihapus!');
    }

    public function toggleStatus($id)
    {
        $lowongan = CareerPosition::findOrFail($id);
        $lowongan->status = $lowongan->status === 'open' ? 'closed' : 'open';
        $lowongan->save();

        return response()->json([
            'success' => true,
            'message' => 'Status lowongan berhasil diubah!',
            'new_status' => $lowongan->status
        ]);
    }
}