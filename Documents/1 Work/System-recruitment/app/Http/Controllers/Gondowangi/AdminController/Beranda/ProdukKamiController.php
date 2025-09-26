<?php

namespace App\Http\Controllers\Gondowangi\AdminController\Beranda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class ProdukKamiController extends Controller
{
    public function index()
    {
        // Ambil statistik brand
        $totalBrands = Brand::count();
        $activeBrands = Brand::where('is_active', true)->count();
        
        // Ambil semua brand dengan relasi categories melalui products
        $brands = Brand::with(['products.category'])
            ->orderBy('display_order')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Ambil semua categories untuk dropdown
        $categories = ProductCategory::where('is_active', true)
            ->orderBy('category_name')
            ->get();
        
        return view('Gondowangi.Admin.Beranda.ProdukKami', compact(
            'totalBrands',
            'activeBrands', 
            'brands',
            'categories'
        ));
    }
    
    public function show(Brand $brand)
    {
        $brand->load(['products.category']);
        
        return response()->json([
            'success' => true,
            'brand' => $brand
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'brand_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'website_url' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);
        
        $data = [
            'brand_name' => $request->brand_name,
            'brand_slug' => Str::slug($request->brand_name),
            'description' => $request->description,
            'website_url' => $request->website_url,
            'is_active' => $request->is_active ?? true,
            'display_order' => Brand::max('display_order') + 1
        ];
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo      = $request->file('logo');
            $filename  = time() . '_' . Str::slug($request->brand_name) . '.' . $logo->getClientOriginalExtension();
            $path      = 'assets/logo';
        
            // Simpan file ke public/assets/logo/
            $logo->move(public_path($path), $filename);
        
            // Simpan path relatif ke DB
            $data['logo_url'] = $path . '/' . $filename;
        }

        
        Brand::create($data);
        
        return response()->json([
            'success' => true,
            'message' => 'Brand berhasil ditambahkan!'
        ]);
    }
    
    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'brand_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'website_url' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);
        
        $data = [
            'brand_name' => $request->brand_name,
            'brand_slug' => Str::slug($request->brand_name),
            'description' => $request->description,
            'website_url' => $request->website_url,
            'is_active' => $request->is_active ?? true
        ];
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($brand->logo_url && Storage::disk('public')->exists($brand->logo_url)) {
                Storage::disk('public')->delete($brand->logo_url);
            }
            
            $logoPath = $request->file('logo')->store('brands/logos', 'public');
            $data['logo_url'] = $logoPath;
        }
        
        $brand->update($data);
        
        return response()->json([
            'success' => true,
            'message' => 'Brand berhasil diperbarui!'
        ]);
    }
    
    public function destroy(Brand $brand)
    {
        // Delete logo file if exists
        if ($brand->logo_url && Storage::disk('public')->exists($brand->logo_url)) {
            Storage::disk('public')->delete($brand->logo_url);
        }
        
        $brand->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Brand berhasil dihapus!'
        ]);
    }
    
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $brands = Brand::with(['products.category'])
            ->where('brand_name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orderBy('display_order')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return response()->json([
            'success' => true,
            'data' => $brands
        ]);
    }
}
