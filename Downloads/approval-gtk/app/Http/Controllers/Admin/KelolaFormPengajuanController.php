<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormField;
use App\Models\KategoriPengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KelolaFormPengajuanController extends Controller
{
    public function index()
    {
        $kategoriPengajuan = KategoriPengajuan::where('status', 'aktif')
            ->with(['formFields' => function($query) {
                $query->orderBy('urutan');
            }])
            ->get();
            
        return view('Approval-app.HelpDesk.KelolaFormPengajuan.index', compact('kategoriPengajuan'));
    }

    public function create()
    {
        $kategoriPengajuan = KategoriPengajuan::where('status', 'aktif')->get();
        
        return view('Approval-app.HelpDesk.KelolaFormPengajuan.create', compact('kategoriPengajuan'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_pengajuan_id' => 'required|exists:KategoriPengajuan,id',
            'nama_field' => 'required|string|max:100',
            'label' => 'required|string|max:100',
            'tipe_field' => 'required|in:text,textarea,number,date,select,radio,checkbox,file,currency',
            'placeholder' => 'nullable|string|max:200',
            'validasi' => 'nullable|array',
            'opsi' => 'nullable|array',
            'urutan' => 'required|integer|min:1',
            'posisi_row' => 'required|integer|min:1',
            'posisi_col' => 'required|integer|min:1|max:12',
            'lebar_col' => 'required|integer|min:1|max:12',
            'wajib' => 'required|boolean',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            // Convert opsi array menjadi JSON jika ada
            if ($request->has('opsi') && is_array($request->opsi)) {
                $data['opsi'] = array_filter($request->opsi);
            }

            // Convert validasi array menjadi JSON jika ada
            if ($request->has('validasi') && is_array($request->validasi)) {
                $data['validasi'] = array_filter($request->validasi);
            }

            FormField::create($data);

            DB::commit();

            return redirect()->route('kelola-form-pengajuan.index')
                ->with('success', 'Form field berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $kategori = KategoriPengajuan::with(['formFields' => function($query) {
            $query->where('status', 'aktif')->orderBy('posisi_row')->orderBy('posisi_col');
        }])->findOrFail($id);

        return view('Approval-app.HelpDesk.KelolaFormPengajuan.show', compact('kategori'));
    }

    public function edit($id)
    {
        $formField = FormField::findOrFail($id);
        $kategoriPengajuan = KategoriPengajuan::where('status', 'aktif')->get();
        
        return view('Approval-app.HelpDesk.KelolaFormPengajuan.edit', compact('formField', 'kategoriPengajuan'));
    }

    public function update(Request $request, $id)
    {
        $formField = FormField::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'kategori_pengajuan_id' => 'required|exists:KategoriPengajuan,id',
            'nama_field' => 'required|string|max:100',
            'label' => 'required|string|max:100',
            'tipe_field' => 'required|in:text,textarea,number,date,select,radio,checkbox,file,currency',
            'placeholder' => 'nullable|string|max:200',
            'validasi' => 'nullable|array',
            'opsi' => 'nullable|array',
            'urutan' => 'required|integer|min:1',
            'posisi_row' => 'required|integer|min:1',
            'posisi_col' => 'required|integer|min:1|max:12',
            'lebar_col' => 'required|integer|min:1|max:12',
            'wajib' => 'required|boolean',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            // Convert opsi array menjadi JSON jika ada
            if ($request->has('opsi') && is_array($request->opsi)) {
                $data['opsi'] = array_filter($request->opsi);
            }

            // Convert validasi array menjadi JSON jika ada
            if ($request->has('validasi') && is_array($request->validasi)) {
                $data['validasi'] = array_filter($request->validasi);
            }

            $formField->update($data);

            DB::commit();

            return redirect()->route('kelola-form-pengajuan.index')
                ->with('success', 'Form field berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $formField = FormField::findOrFail($id);
            $formField->delete();

            return response()->json([
                'success' => true,
                'message' => 'Form field berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function preview($kategoriId)
    {
        $kategori = KategoriPengajuan::with(['formFields' => function($query) {
            $query->where('status', 'aktif')
                ->orderBy('posisi_row')
                ->orderBy('posisi_col');
        }])->findOrFail($kategoriId);

        return view('Approval-app.HelpDesk.KelolaFormPengajuan.preview', compact('kategori'));
    }
}