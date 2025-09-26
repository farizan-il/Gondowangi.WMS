<?php

namespace App\Http\Controllers\Gondowangi\AdminController\Beranda;

use App\Http\Controllers\Controller;
use App\Models\Award;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AwardController extends Controller
{
    public function index(Request $request)
    {
        // Query untuk mengambil awards dengan filter
        $query = Award::query();
        
        // Filter berdasarkan kategori
        if ($request->filled('category')) {
            $query->where('award_name', 'LIKE', '%' . $request->category . '%');
        }
        
        // Filter berdasarkan tahun
        if ($request->filled('year')) {
            $query->whereYear('award_date', $request->year);
        }
        
        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('award_name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('award_description', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('awarding_body', 'LIKE', '%' . $request->search . '%');
            });
        }
        
        // Filter berdasarkan status aktif/non-aktif
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
            // Jika 'all' atau tidak diisi, tampilkan semua
        }
        
        // PENTING: Hapus filter is_active yang otomatis, karena di admin harus tampil semua
        // Urutkan berdasarkan status aktif dulu, lalu display_order dan tanggal
        $awards = $query->orderBy('is_active', 'desc') // Aktif di atas
            ->orderBy('display_order', 'asc')
            ->orderBy('award_date', 'desc')
            ->paginate(9);
        
        // Statistik untuk cards - tetap hitung semua untuk admin insight
        $totalAwards = Award::count(); // Total semua award
        $activeAwards = Award::where('is_active', true)->count();
        $inactiveAwards = Award::where('is_active', false)->count();
        
        $thisYearAwards = Award::whereYear('award_date', Carbon::now()->year)->count();
        $thisYearActiveAwards = Award::where('is_active', true)
            ->whereYear('award_date', Carbon::now()->year)
            ->count();
        
        // Kategori unik (dari semua award, bukan hanya yang aktif)
        $categories = Award::selectRaw("
              CASE 
                  WHEN LOWER(award_name) LIKE '%top brand%' THEN 'top-brand'
                  WHEN LOWER(award_name) LIKE '%stellar%' THEN 'stellar'
                  WHEN LOWER(award_name) LIKE '%excellence%' OR LOWER(award_name) LIKE '%achievement%' THEN 'achievement'
                  WHEN LOWER(award_name) LIKE '%certification%' OR LOWER(award_name) LIKE '%certified%' THEN 'certification'
                  ELSE 'other'
              END as category
            ")
            ->groupBy('category')
            ->get()
            ->count();
        
        // Tahun-tahun yang tersedia (dari semua award)
        $availableYears = Award::selectRaw('YEAR(award_date) as year')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        return view('Gondowangi.Admin.Beranda.Award', [
            'title' => 'Beranda',
            'awards' => $awards,
            'totalAwards' => $totalAwards,
            'activeAwards' => $activeAwards,
            'inactiveAwards' => $inactiveAwards,
            'thisYearAwards' => $thisYearAwards,
            'thisYearActiveAwards' => $thisYearActiveAwards,
            'categories' => $categories,
            'availableYears' => $availableYears,
            'currentFilters' => [
                'category' => $request->get('category'),
                'year' => $request->get('year'),
                'search' => $request->get('search'),
                'status' => $request->get('status')
            ]
        ]);
    }
    
    public function toggleStatus(Award $award)
    {
        try {
            $award->update([
                'is_active' => !$award->is_active
            ]);
    
            $status = $award->is_active ? 'activated' : 'deactivated';
            
            return redirect()->back()->with('success', "Award '{$award->award_name}' has been {$status} successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update award status.');
        }
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'award_name' => 'required|string|max:255',
            'award_description' => 'nullable|string',
            'award_date' => 'required|date',
            'awarding_body' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'display_order' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);
        
        $data = $request->only([
            'award_name', 'award_description', 'award_date', 
            'awarding_body', 'display_order', 'is_active'
        ]);
        
        // Handle file upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $destinationPath = public_path('assets/award');
            
            // Pastikan foldernya ada
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
        
            $image->move($destinationPath, $imageName);
            $data['image_url'] = 'assets/award/' . $imageName;
        }
        
        // Set default display_order jika tidak diisi
        // if (!$data['display_order']) {
        //     $data['display_order'] = Award::max('display_order') + 1;
        // }
        
        Award::create($data);
        
        return redirect()->route('awards.index')
                        ->with('success', 'Award berhasil ditambahkan!');
    }
    
    public function update(Request $request, Award $award)
    {
        $request->validate([
            'award_name' => 'required|string|max:255',
            'award_description' => 'nullable|string',
            'award_date' => 'required|date',
            'awarding_body' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'display_order' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);
        
        $data = $request->only([
            'award_name', 'award_description', 'award_date', 
            'awarding_body', 'display_order', 'is_active'
        ]);
        
        // Handle file upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($award->image_url) {
                $oldImagePath = str_replace('/storage/', '', $award->image_url);
                Storage::disk('public')->delete($oldImagePath);
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('awards', $imageName, 'public');
            $data['image_url'] = Storage::url($imagePath);
        }
        
        $award->update($data);
        
        return redirect()->route('awards.index')
                        ->with('success', 'Award berhasil diperbarui!');
    }
    
    public function destroy(Award $award)
    {
        // Delete image if exists
        if ($award->image_url) {
            $imagePath = str_replace('/storage/', '', $award->image_url);
            Storage::disk('public')->delete($imagePath);
        }
        
        $award->delete();
        
        return redirect()->route('awards.index')
                        ->with('success', 'Award berhasil dihapus!');
    }
}
