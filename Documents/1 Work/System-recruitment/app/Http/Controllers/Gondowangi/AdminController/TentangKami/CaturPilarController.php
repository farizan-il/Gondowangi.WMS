<?php

namespace App\Http\Controllers\Gondowangi\AdminController\TentangKami;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaturPilar;

class CaturPilarController extends Controller
{
    public function index()
    {
        $caturPilar = CaturPilar::orderBy('urutan', 'asc')->get();
        
        return view('Gondowangi.Admin.TentangKami.CaturPilar', [
            'title' => 'Catur Pilar',
            'caturPilar' => $caturPilar
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'icon' => 'required|string|max:50',
            'urutan' => 'required|integer|min:1',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        CaturPilar::create($request->all());

        return redirect()->route('admin.catur-pilar.index')
                        ->with('success', 'Data Catur Pilar berhasil ditambahkan!');
    }

    public function update(Request $request, CaturPilar $caturPilar)
    {
        $request->validate([
            'judul' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'icon' => 'required|string|max:50',
            'urutan' => 'required|integer|min:1',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        $caturPilar->update($request->all());

        return redirect()->route('admin.catur-pilar.index')
                        ->with('success', 'Data Catur Pilar berhasil diperbarui!');
    }

    public function destroy(CaturPilar $caturPilar)
    {
        $caturPilar->delete();

        return redirect()->route('admin.catur-pilar.index')
                        ->with('success', 'Data Catur Pilar berhasil dihapus!');
    }
}
