<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Golongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KelolaNominalController extends Controller
{
    public function index()
    {
        $nominal = Golongan::orderBy('is_active')->get();
            
        return view('Approval-app.HelpDesk.KelolaNominal.index', compact('nominal'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nama_golongan' => 'required|string|max:100|unique:Golongan,nama_golongan',
            'biaya_hotel_per_hari' => 'required|numeric',
            'biaya_makan_per_hari' => 'required|numeric',
        ], [
            'nama_golongan.unique' => 'Nama golongan sudah ada, silakan gunakan nama yang berbeda.',
            'nama_golongan.required' => 'Nama golongan harus diisi.',
            'biaya_hotel_per_hari.required' => 'Biaya hotel per hari harus diisi.',
            'biaya_hotel_per_hari.numeric' => 'Biaya hotel per hari harus berupa angka.',
            'biaya_makan_per_hari.required' => 'Biaya makan per hari harus diisi.',
            'biaya_makan_per_hari.numeric' => 'Biaya makan per hari harus berupa angka.',
        ]);
    
        $golongan = Golongan::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Golongan berhasil ditambahkan.',
            'data' => $golongan
        ]);
    }
    
    public function show($id)
    {
        return response()->json(Golongan::findOrFail($id));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_golongan' => 'required|string|max:100|unique:Golongan,nama_golongan,' . $id,
            'biaya_hotel_per_hari' => 'required|numeric',
            'biaya_makan_per_hari' => 'required|numeric',
        ], [
            'nama_golongan.unique' => 'Nama golongan sudah ada, silakan gunakan nama yang berbeda.',
            'nama_golongan.required' => 'Nama golongan harus diisi.',
            'biaya_hotel_per_hari.required' => 'Biaya hotel per hari harus diisi.',
            'biaya_hotel_per_hari.numeric' => 'Biaya hotel per hari harus berupa angka.',
            'biaya_makan_per_hari.required' => 'Biaya makan per hari harus diisi.',
            'biaya_makan_per_hari.numeric' => 'Biaya makan per hari harus berupa angka.',
        ]);
    
        $golongan = Golongan::findOrFail($id);
        $golongan->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Golongan berhasil diperbarui.',
            'data' => $golongan
        ]);
    }
    
    public function destroy($id)
    {
        $golongan = Golongan::findOrFail($id);
        $golongan->delete();
        return response()->json([
            'success' => true,
            'message' => 'Golongan berhasil dihapus.'
        ]);
    }
}