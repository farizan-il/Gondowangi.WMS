<?php

namespace App\Http\Controllers\Gondowangi\AdminController\TentangKami;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Str;

class TentangKamiBannerController extends Controller
{
    public function index()
    {
        $banners = Banner::where('type', 'tentang_kami')
            ->ordered()
            ->get();
    
        return view('Gondowangi.Admin.TentangKami.TentangKamiBanner', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|url|max:255',
            'sort_order' => 'integer|min:0'
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

        Banner::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'image_path' => $imagePath,
            'button_text' => $request->button_text,
            'button_url' => $request->button_url,
            'is_active' => $request->boolean('is_active', true),
            'type' => 'tentang_kami',
            'sort_order' => $request->sort_order ?? 0
        ]);

        return redirect()->back()->with('success', 'Banner berhasil ditambahkan!');
    }
    
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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
            'sort_order' => $request->sort_order ?? $banner->sort_order
        ]);
    
        return redirect()->back()->with('success', 'Banner berhasil diupdate!');
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
