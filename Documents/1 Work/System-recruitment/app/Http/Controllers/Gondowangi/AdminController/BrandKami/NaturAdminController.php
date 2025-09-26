<?php
namespace App\Http\Controllers\Gondowangi\AdminController\BrandKami;

use App\Http\Controllers\Controller;
use App\Models\BrandDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class NaturAdminController extends Controller
{
    public function index()
    {
        $brandDetails = BrandDetail::orderBy('created_at', 'desc')->get();
        return view('Gondowangi.Admin.BrandKami.natur', compact('brandDetails'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:carousel_brand,banner_brand,detail_brand',
            'brand_name' => 'required|in:Natur,Mizzu,Azalea,Hgforman',
            'title' => 'nullable|string',
            'deksripsi' => 'nullable|string',
            'img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $imagePath = null;
            if ($request->hasFile('img')) {
                $image = $request->file('img');
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('assets/brand_details');
                
                // Buat folder jika belum ada
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
            
                $image->move($destinationPath, $imageName);
                $imagePath = 'assets/brand_details/' . $imageName;
            }

            $brandDetail = BrandDetail::create([
                'title' => $request->title,
                'type' => $request->type,
                'brand_name' => $request->brand_name,
                'deksripsi' => $request->deksripsi,
                'img' => $imagePath
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Brand detail berhasil ditambahkan',
                'data' => $brandDetail
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $brandDetail = BrandDetail::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $brandDetail
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:carousel_brand,banner_brand,detail_brand',
            'brand_name' => 'required|in:Natur,Mizzu,Azalea,Hgforman',
            'title' => 'nullable|string',
            'deksripsi' => 'nullable|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $brandDetail = BrandDetail::findOrFail($id);
            $updateData = [
                'type' => $request->type,
                'brand_name' => $request->brand_name,
                'title' => $request->title,
                'deksripsi' => $request->deksripsi,
            ];

            if ($request->hasFile('img')) {
                // Hapus gambar lama jika ada
                if ($brandDetail->img && file_exists(public_path($brandDetail->img))) {
                    unlink(public_path($brandDetail->img));
                }
            
                // Upload gambar baru
                $image = $request->file('img');
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('assets/brand_details');
            
                // Buat folder jika belum ada
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
            
                $image->move($destinationPath, $imageName);
                $updateData['img'] = 'assets/brand_details/' . $imageName;
            }

            $brandDetail->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Brand detail berhasil diperbarui',
                'data' => $brandDetail
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $brandDetail = BrandDetail::findOrFail($id);
            
            // Hapus gambar jika ada
            if ($brandDetail->img && Storage::disk('public')->exists($brandDetail->img)) {
                Storage::disk('public')->delete($brandDetail->img);
            }

            $brandDetail->delete();

            return response()->json([
                'success' => true,
                'message' => 'Brand detail berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}