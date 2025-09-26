<?php

namespace App\Http\Controllers\Gondowangi\AdminController\Footer;

use App\Http\Controllers\Controller;
use App\Models\Footer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FooterController extends Controller
{
    public function index()
    {
        // Get the first footer or create a default one
        $footer = Footer::first();
        
        if (!$footer) {
            // Create default footer if none exists
            $footer = Footer::create([
                'company_name' => 'Nama Perusahaan',
                'description' => 'Deskripsi perusahaan akan tampil di sini...',
                'address' => 'Alamat perusahaan',
                'phone' => '',
                'email' => '',
                'facebook_url' => '',
                'instagram_url' => '',
                'youtube_url' => '',
                'linkedin_url' => '',
                'copyright_text' => '2025. All Rights Reserved.',
                'status' => true
            ]);
        }
        
        return view('Gondowangi.Admin.Footer.index', compact('footer'));
    }

    /**
     * Update the footer information.
     * Since we only have one footer, we always update the first one.
     */
   public function update(Request $request, $id = null)
    {
        // Validasi input dari request
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'copyright_text' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'nullable|boolean'
        ]);
    
        // Jika validasi gagal, kembali ke form sebelumnya dengan pesan error
        if ($validator->fails()) {
            dd($validator->errors()); // Debug validasi error
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        // Ambil data footer pertama atau buat baru jika tidak ada
        $footer = Footer::first();
        if (!$footer) {
            $footer = new Footer();
        }
    
        // Ambil data input selain token dan metode
        $data = $request->except('_token', '_method');
        
        // Menangani checkbox status (jika tidak dicentang, maka tidak akan ada dalam request)
        $data['status'] = $request->has('status') ? 1 : 0;
    
        // Menangani upload logo
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($footer->logo) {
                Storage::delete('public/footer/' . $footer->logo);
            }
            
            // Upload logo baru
            $logo = $request->file('logo');
            $logoName = time() . '_' . $logo->getClientOriginalName();
            $logo->storeAs('public/footer', $logoName);
            $data['logo'] = $logoName;
        }
    
        // Periksa apakah footer sudah ada, jika ada perbarui, jika tidak buat baru
        if ($footer->exists) {
            $footer->update($data);
            $message = 'Footer berhasil diperbarui!';
        } else {
            Footer::create($data);
            $message = 'Footer berhasil dibuat!';
        }
    
        // Kembalikan ke halaman dengan pesan sukses
        return redirect()->back()->with('success', $message);
    }


    /**
     * Toggle footer status (active/inactive)
     */
    public function toggleStatus()
    {
        $footer = Footer::first();
        
        if (!$footer) {
            return redirect()->route('admin.footer.index')
                ->with('error', 'Footer tidak ditemukan!');
        }

        $footer->update([
            'status' => !$footer->status
        ]);

        $status = $footer->status ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->route('admin.footer.index')
            ->with('success', "Footer berhasil {$status}!");
    }

    /**
     * Get active footer for public display
     * This method can be used by other controllers
     */
    public static function getActiveFooter()
    {
        return Footer::where('status', true)->first();
    }
    
    /**
     * Get footer data for public display
     * This can be used in any controller that needs footer data
     */
    public static function getFooterData()
    {
        $footer = self::getActiveFooter();
        
        if (!$footer) {
            // Return default footer data if none exists
            return [
                'company_name' => 'Nama Perusahaan',
                'description' => 'Deskripsi perusahaan akan tampil di sini...',
                'address' => 'Alamat perusahaan',
                'phone' => '',
                'email' => '',
                'facebook_url' => '',
                'instagram_url' => '',
                'youtube_url' => '',
                'linkedin_url' => '',
                'copyright_text' => '2025. All Rights Reserved.',
                'logo_url' => asset('images/default-logo.png'),
                'status' => false
            ];
        }
        
        return [
            'company_name' => $footer->company_name,
            'description' => $footer->description,
            'address' => $footer->address,
            'phone' => $footer->phone,
            'email' => $footer->email,
            'facebook_url' => $footer->facebook_url,
            'instagram_url' => $footer->instagram_url,
            'youtube_url' => $footer->youtube_url,
            'linkedin_url' => $footer->linkedin_url,
            'copyright_text' => $footer->copyright_text,
            'logo_url' => $footer->logo_url,
            'status' => $footer->status
        ];
    }
}