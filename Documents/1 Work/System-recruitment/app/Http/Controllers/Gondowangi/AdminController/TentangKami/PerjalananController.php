<?php

namespace App\Http\Controllers\Gondowangi\AdminController\TentangKami;

use App\Http\Controllers\Controller;
use App\Models\CompanyTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PerjalananController extends Controller
{
    public function index()
    {
        $perjalanan = CompanyTimeline::orderByYear('desc')->get();
        
        return view('Gondowangi.Admin.TentangKami.Perjalanan', [
            'title' => 'Perjalanan Perusahaan',
            'perjalanan' => $perjalanan
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            // 'timeline_type' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only(['year', 'title', 'description', 'timeline_type']);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            
            // Pastikan direktori ada
            $publicPath = public_path('assets/perjalanan');
            if (!File::exists($publicPath)) {
                File::makeDirectory($publicPath, 0755, true);
            }
            
            $image->move($publicPath, $imageName);
            $data['image_url'] = 'assets/perjalanan/' . $imageName;
        }

        CompanyTimeline::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data perjalanan berhasil ditambahkan!'
        ]);
    }

    public function show($id)
    {
        $perjalanan = CompanyTimeline::findOrFail($id);
        return response()->json($perjalanan);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'timeline_type' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $perjalanan = CompanyTimeline::findOrFail($id);
        $data = $request->only(['year', 'title', 'description', 'timeline_type']);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($perjalanan->image_url) {
                $oldImagePath = public_path($perjalanan->getRawOriginal('image_url'));
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            
            // Pastikan direktori ada
            $publicPath = public_path('assets/perjalanan');
            if (!File::exists($publicPath)) {
                File::makeDirectory($publicPath, 0755, true);
            }
            
            $image->move($publicPath, $imageName);
            $data['image_url'] = 'assets/perjalanan/' . $imageName;
        }

        $perjalanan->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data perjalanan berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $perjalanan = CompanyTimeline::findOrFail($id);

        // Hapus gambar jika ada
        if ($perjalanan->image_url) {
            $imagePath = public_path($perjalanan->getRawOriginal('image_url'));
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        $perjalanan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data perjalanan berhasil dihapus!'
        ]);
    }
}