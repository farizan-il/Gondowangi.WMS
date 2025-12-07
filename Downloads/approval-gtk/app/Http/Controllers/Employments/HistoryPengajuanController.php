<?php

namespace App\Http\Controllers\Employments;

use App\Http\Controllers\Controller;
use App\Models\KategoriPengajuan;
use App\Models\FormField;
use App\Models\Pengajuan;
use App\Models\DetailPengajuan;
use App\Models\HistoryPengajuan;
use App\Models\Department;
use App\Models\TransactionRequest;
use App\Models\RoleLevel;
use App\Models\Karyawan;
use App\Models\FlowApproval;
use App\Models\ProgressApproval;
use App\Models\EmailNotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\PengajuanBaruNotification;
use App\Jobs\SendPengajuanEmailNotification;
use Illuminate\Support\Facades\Storage;

// controller untuk membuat pengajuan
class HistoryPengajuanController extends Controller
{
    public function index()
    {
        // Ambil data kategori pengajuan
        $kategoriPengajuan = KategoriPengajuan::aktif()
            ->orderBy('nama')
            ->get();
    
        // Ambil data pengajuan milik user yang sedang login DAN sudah completed
        $pengajuanList = Pengajuan::with([
            'kategoriPengajuan',
            'requester',
            'settlement',
            'transactionRequest',
            'emailNotificationLogs',
            'historyPengajuan' => function($query) {
                $query->latest('created_at');
            }
        ])
        ->where('requester_id', Auth::id())
        ->where('status_pengajuan', 'completed') // Tambahan filter status
        ->orderBy('created_at', 'desc')
        ->get();
        
        // Hitung data belum completed
        $pendingPengajuan = \App\Models\Pengajuan::where('status_pengajuan', '!=', 'completed')->count();
        $pendingSettlement = \App\Models\Settlement::where('status_settlement', '!=', 'approved')->count();
        
        $totalPending = $pendingPengajuan + $pendingSettlement;
    
        return view('Approval-app.Karyawan.HistoryPengajuan.index', 
            compact('kategoriPengajuan', 'pengajuanList', 'totalPending', 'pendingPengajuan', 'pendingSettlement'));
    }
}
    
    
    
    
    
    
    
    
    
    