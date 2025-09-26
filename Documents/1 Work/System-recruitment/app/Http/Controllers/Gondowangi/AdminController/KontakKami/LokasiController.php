<?php
namespace App\Http\Controllers\Gondowangi\AdminController\KontakKami;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LokasiController extends Controller
{
    public function index()
    {
        $contacts = Contact::orderBy('created_at', 'desc')->get();
        return view('Gondowangi.Admin.KontakKami.kontakkami', compact('contacts'));
    }

    public function show($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'contact' => [
                        'id' => $contact->id,
                        'nama_lengkap' => $contact->nama_lengkap,
                        'alamat_email' => $contact->alamat_email,
                        'subjek' => $contact->subjek,
                        'komentar_pesan' => $contact->komentar_pesan,
                        'untuk' => $contact->untuk,
                        'status' => $contact->status,
                        'is_read' => $contact->is_read,
                        'created_at' => $contact->created_at->toISOString(),
                        'replied_at' => $contact->replied_at ? $contact->replied_at->toISOString() : null,
                        'replied_by' => $contact->replied_by
                    ]
                ]);
            }
            
            // Return view untuk non-AJAX request (jika ada)
            return view('your.detail.view', compact('contact'));
            
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contact not found'
                ], 404);
            }
            
            return redirect()->back()->with('error', 'Contact not found');
        }
    }
    
    public function getUnreadCount()
    {
        try {
            $count = Contact::where('status', 'baru')
               ->where('is_read', false)
               ->count();
            
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['count' => 0, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage (DELETE).
     */
    public function destroy($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kontak berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus kontak'
            ], 500);
        }
    }

    /**
     * Mark contact as read
     */
    public function markAsRead($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Kontak berhasil ditandai sebagai dibaca'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menandai sebagai dibaca'
            ], 500);
        }
    }
    
    public function markAsComplete(Request $request, $id)
    {
        try {
            $contact = Contact::findOrFail($id);
            
            // Update status menjadi resolved dan mark as read
            $contact->update([
                'status' => 'selesai',
                'is_read' => true,
                'replied_at' => now(),
                'replied_by' => auth()->id()
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diupdate menjadi selesai',
                'contact' => [
                    'id' => $contact->id,
                    'status' => $contact->status,
                    'is_read' => $contact->is_read,
                    'status_label' => $contact->status == 'resolved' ? 'Selesai' : $contact->status
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status: ' . $e->getMessage()
            ], 500);
        }
    }
}
