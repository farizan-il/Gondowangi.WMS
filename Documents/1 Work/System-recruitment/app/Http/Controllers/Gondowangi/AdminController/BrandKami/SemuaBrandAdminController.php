<?php

namespace App\Http\Controllers\Gondowangi\AdminController\BrandKami;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SemuaBrandAdminController extends Controller
{
    public function index()
    {
        $banners = Banner::where('type', 'semua_brand')
            ->ordered()
            ->get();
            
        $brandCarousel = Banner::where('type', 'brand_produk')
            ->ordered()
            ->get();
            
        $brands = Brand::orderBy('display_order', 'asc')
            ->orderBy('brand_name', 'asc')
            ->get();
    
        return view('Gondowangi.Admin.BrandKami.semuabrand', compact('banners', 'brands', 'brandCarousel'));
    }
    
    public function storeBrandImg(Request $request)
    {
        // Validasi input
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:9048',
        ], [
            'image.required' => 'Gambar wajib diunggah',
            'image.image' => 'File harus berupa gambar',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif',
            'image.max' => 'Ukuran gambar maksimal 2MB'
        ]);

        try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                
                // Generate nama file yang unik
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                
                // Simpan gambar ke public/assets/banners
                $imagePath = $image->move(public_path('assets/banners'), $imageName);
                
                // Simpan ke database
                Banner::create([
                    'image_path' => 'assets/banners/' . $imageName,
                    'type' => 'brand_produk',
                    'is_active' => true,
                    'display_order' => Banner::where('type', 'brand_produk')->count() + 1
                ]);

                return redirect()->back()->with('success', 'Carousel brand berhasil ditambahkan!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan gambar: ' . $e->getMessage());
        }

        return redirect()->back()->with('error', 'Gagal menyimpan gambar');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:9048',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|url|max:255',
            'sort_order' => 'integer|min:0',
        ]);
    
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('assets/banners');
    
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
    
            $image->move($destinationPath, $imageName);
            $imagePath = 'assets/banners/' . $imageName;
        }
    
        $test = Banner::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'image_path' => $imagePath,
            'button_text' => $request->button_text,
            'button_url' => $request->button_url,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
            'type' => 'semua_brand',
        ]);
        
    
        return redirect()->back()->with('success', 'Banner berhasil ditambahkan!');
    }
    
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:9048',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);
    
        $imagePath = $banner->image_path;
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            $oldImagePath = public_path($banner->image_path);
            if ($banner->image_path && file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
    
            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('assets/banners');
    
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
    
            $image->move($destinationPath, $imageName);
            $imagePath = 'assets/banners/' . $imageName;
        }
    
        $banner->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'image_path' => $imagePath,
            'button_text' => $request->button_text,
            'button_url' => $request->button_url,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? $banner->sort_order,
            'type' => 'semua_brand', // ✅ tetap dipaksa jadi 'semua_brand' saat update
        ]);
    
        return redirect()->back()->with('success', 'Banner berhasil diupdate!');
    }
    
    public function updateImage(Request $request, $id) {
       $request->validate([
           'brand_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:9048',
       ], [
           'brand_img.required' => 'Gambar brand harus dipilih.',
           'brand_img.image' => 'File harus berupa gambar.',
           'brand_img.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
           'brand_img.max' => 'Ukuran gambar maksimal 2MB.',
       ]);
    
       $brand = Brand::findOrFail($id);
    
       try {
           // Hapus gambar lama jika ada
           if ($brand->brand_img && file_exists(public_path('assets/brand/' . $brand->brand_img))) {
               unlink(public_path('assets/brand/' . $brand->brand_img));
           }
    
           // Upload gambar baru
           if ($request->hasFile('brand_img')) {
               $image = $request->file('brand_img');
               $imageName = 'brand_' . $brand->id . '_' . time() . '_' . Str::random(10) . '.' . $image->extension();
               $image->move(public_path('assets/brand'), $imageName);
    
               // Update database
               $brand->update([
                   'brand_img' => $imageName
               ]);
           }
    
           return redirect()->back()->with('success', 'Gambar brand "' . $brand->brand_name . '" berhasil diperbarui.');
    
       } catch (\Exception $e) {
           return redirect()->back()->with('error', 'Gagal memperbarui gambar brand: ' . $e->getMessage());
       }
    }
    
    public function removeImage($id) {
       $brand = Brand::findOrFail($id);
    
       try {
           // Hapus file gambar dari folder public
           if ($brand->brand_img && file_exists(public_path('assets/brand/' . $brand->brand_img))) {
               unlink(public_path('assets/brand/' . $brand->brand_img));
           }
    
           // Update database
           $brand->update([
               'brand_img' => null
           ]);
    
           return redirect()->back()->with('success', 'Gambar brand "' . $brand->brand_name . '" berhasil dihapus.');
    
       } catch (\Exception $e) {
           return redirect()->back()->with('error', 'Gagal menghapus gambar brand: ' . $e->getMessage());
       }
    }


    public function destroy(Banner $banner)
    {
        // Hapus gambar dari storage
        if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $banner->delete();

        return redirect()->back()->with('success', 'Banner berhasil dihapus!');
    }

    public function toggleStatus(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);
        
        $status = $banner->is_active ? 'aktif' : 'nonaktif';
        return redirect()->back()->with('success', "Banner berhasil di{$status}kan!");
    }
}