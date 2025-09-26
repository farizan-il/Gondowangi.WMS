<?php
namespace App\Http\Controllers\Gondowangi\AdminController\Beranda;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Exception;

class AcaraMendatangController extends Controller
{
    public function index()
    {
        try {
            $events = Event::orderBy('event_date', 'desc')->paginate(10);
            $totalEvents = Event::count();
            $activeEvents = Event::where('status', 'upcoming')->count();
            $featuredEvents = Event::where('is_featured', true)->count();
            
            return view('Gondowangi.Admin.Beranda.AcaraMendatang', compact(
                'events', 
                'totalEvents', 
                'activeEvents', 
                'featuredEvents'
            ));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data: ' . $e->getMessage());
        }
    }
    
    public function toggleFeatured(Request $request, $id)
    {
        $event = Event::findOrFail($id);
    
        // if (! $event->is_featured) {
        //     $totalFeatured = Event::where('is_featured', true)->count();
    
        //     if ($totalFeatured >= 3) {
        //         return response()->json([
        //             'status' => 'limit',
        //             'message' => 'Maksimal hanya 3 acara yang bisa dipublikasikan. Silakan pilih acara lain untuk diganti.'
        //         ], 400);
        //     }
        // }
    
        $event->is_featured = ! $event->is_featured;
        $event->save();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Status publikasi acara diperbarui.'
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'event_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'event_date' => 'required|date|after_or_equal:today',
                'event_time' => 'nullable|date_format:H:i',
                'location' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'status' => 'required|in:upcoming,ongoing,completed,cancelled',
                'is_featured' => 'boolean'
            ]);

            $data = $request->all();
            // $data['event_slug'] = Str::slug($request->event_name);
            $data['is_featured'] = $request->has('is_featured');

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $destinationPath = public_path('assets/acaramendatang');
                $image->move($destinationPath, $imageName);
            
                // Simpan path relatif ke database
                $data['image_url'] = 'assets/acaramendatang/' . $imageName;
            }

            $event = Event::create($data);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Event berhasil dibuat!',
                    'data' => $event
                ]);
            }

            return redirect()->route('admin.acara-mendatang.index')
                ->with('success', 'Event berhasil dibuat!');
        } 
        catch (Exception $e) {
            \Log::error('Gagal menyimpan event', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                    'trace' => $e->getTrace()
                ], 500);
            }
        
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->with('trace', $e->getTraceAsString());
        

        }
    }

    public function show($id)
    {
        try {
            $event = Event::findOrFail($id);
            
            if (request()->ajax()) {
                return response()->json([
                    'id' => $event->id,
                    'event_name' => $event->event_name,
                    'description' => $event->description,
                    'event_date' => $event->event_date->format('Y-m-d'),
                    'event_time' => $event->event_time ? $event->event_time->format('H:i') : null,
                    'location' => $event->location,
                    'image_url' => $event->image_url,
                    'status' => $event->status,
                    'is_featured' => $event->is_featured,
                    'event_slug' => $event->event_slug,
                    'created_at' => $event->created_at->format('d M Y H:i'),
                    'updated_at' => $event->updated_at->format('d M Y H:i')
                ]);
            }

            return view('Gondowangi.Admin.Beranda.AcaraMendatang.show', compact('event'));
        } catch (Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memuat data event: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Event tidak ditemukan');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $event = Event::findOrFail($id);
            
            $request->validate([
                'event_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'event_date' => 'required|date|after_or_equal:today',
                'event_time' => 'nullable|date_format:H:i',
                'location' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'status' => 'required|in:upcoming,ongoing,completed,cancelled',
                'is_featured' => 'boolean'
            ]);

            $data = $request->all();
            $data['event_slug'] = Str::slug($request->event_name);
            $data['is_featured'] = $request->has('is_featured');

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($event->image_url) {
                    Storage::disk('public')->delete($event->image_url);
                }
                
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('events', $imageName, 'public');
                $data['image_url'] = $imagePath;
            }

            // Check featured events limit
            if ($data['is_featured'] && !$event->is_featured) {
                $featuredCount = Event::where('is_featured', true)->count();
                if ($featuredCount >= 3) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Maksimal hanya 3 event yang dapat ditampilkan sebagai featured!'
                        ], 422);
                    }
                    return redirect()->back()->with('error', 'Maksimal hanya 3 event yang dapat ditampilkan sebagai featured!');
                }
            }

            $event->update($data);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Event berhasil diupdate!',
                    'data' => $event
                ]);
            }

            return redirect()->route('admin.acara-mendatang.index')
                ->with('success', 'Event berhasil diupdate!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $event = Event::findOrFail($id);
            
            // Delete image if exists
            if ($event->image_url) {
                Storage::disk('public')->delete($event->image_url);
            }

            $event->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Event berhasil dihapus!'
                ]);
            }

            return redirect()->route('admin.acara-mendatang.index')
                ->with('success', 'Event berhasil dihapus!');
        } catch (Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method untuk mendapatkan featured events untuk frontend
    public function getFeaturedEvents()
    {
        try {
            $events = Event::where('is_featured', true)
                ->where('status', 'upcoming')
                ->where('event_date', '>=', now())
                ->orderBy('event_date', 'asc')
                ->get();

            return response()->json($events);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}