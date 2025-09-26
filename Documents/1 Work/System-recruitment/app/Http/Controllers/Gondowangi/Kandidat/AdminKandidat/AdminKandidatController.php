<?php

namespace App\Http\Controllers\Gondowangi\Kandidat\AdminKandidat;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\CareerPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminKandidatController extends Controller
{
    // public function dashboard()
    // {
    //     // Ambil statistik untuk dashboard
    //     $totalKaryawan = Karyawan::count();
    //     $menungguVerifikasi = Karyawan::where('status', 'pending')->count();
    //     $terverifikasi = Karyawan::where('status', 'approved')->count();
    //     $baruHariIni = Karyawan::whereDate('created_at', today())->count();
        
    //     // Tambahkan data untuk lowongan aktif
    //     $lowonganAktif = CareerPosition::where('status', 'active')->count();

    //     // Data untuk chart bulanan (5 bulan terakhir)
    //     $monthlyData = [];
    //     for ($i = 4; $i >= 0; $i--) {
    //         $date = now()->subMonths($i);
    //         $count = Karyawan::whereYear('created_at', $date->year)
    //             ->whereMonth('created_at', $date->month)
    //             ->count();
    //         $monthlyData[] = [
    //             'month' => $date->format('M'),
    //             'count' => $count
    //         ];
    //     }

    //     // Data aktivitas terbaru (5 data terakhir)
    //     $aktivitasTerbaru = Karyawan::orderBy('created_at', 'desc')
    //         ->take(5)
    //         ->get();

    //     // Data distribusi kandidat per kota (langsung dari database)
    //     $kandidatPerKota = Karyawan::select('kota_domisili', DB::raw('count(*) as total'))
    //         ->whereNotNull('kota_domisili')
    //         ->where('kota_domisili', '!=', '')
    //         ->groupBy('kota_domisili')
    //         ->orderBy('total', 'desc')
    //         ->get();

    //     // Mapping koordinat kota (sesuaikan dengan data yang ada di database)
    //     $koordinatKota = [
    //         'Jakarta' => ['lat' => -6.2088, 'lng' => 106.8456],
    //         'Bandung' => ['lat' => -6.9175, 'lng' => 107.6191],
    //         'Surabaya' => ['lat' => -7.2504, 'lng' => 112.7688],
    //         'Yogyakarta' => ['lat' => -7.7956, 'lng' => 110.3695],
    //         'Medan' => ['lat' => 3.5952, 'lng' => 98.6722],
    //         'Makassar' => ['lat' => -5.1477, 'lng' => 119.4327],
    //         'Denpasar' => ['lat' => -8.6500, 'lng' => 115.2167],
    //         'Palembang' => ['lat' => -2.9761, 'lng' => 104.7754],
    //         'Semarang' => ['lat' => -6.9667, 'lng' => 110.4167],
    //         'Malang' => ['lat' => -7.9797, 'lng' => 112.6304],
    //         'Bekasi' => ['lat' => -6.2383, 'lng' => 106.9756],
    //         'Tangerang' => ['lat' => -6.1783, 'lng' => 106.6319],
    //         'KABUPATEN TANGERANG' => ['lat' => -6.1783, 'lng' => 106.6319],
    //         'Bogor' => ['lat' => -6.5950, 'lng' => 106.7966],
    //         'DEPOK, JAWA BARAT' => ['lat' => -6.4025, 'lng' => 106.7942],
    //         'Batam' => ['lat' => 1.1304, 'lng' => 104.0530],
    //         'Pekanbaru' => ['lat' => 0.5071, 'lng' => 101.4478],
    //         'Banjarmasin' => ['lat' => -3.3194, 'lng' => 114.5906],
    //         'Samarinda' => ['lat' => -0.4978, 'lng' => 117.1436],
    //         'Manado' => ['lat' => 1.4748, 'lng' => 124.8421],
    //         'Jayapura' => ['lat' => -2.5489, 'lng' => 140.7197],
    //         'Pontianak' => ['lat' => -0.0263, 'lng' => 109.3425],
    //         'Balikpapan' => ['lat' => -1.2379, 'lng' => 116.8289],
    //         'Padang' => ['lat' => -0.9471, 'lng' => 100.4172],
    //         'Jambi' => ['lat' => -1.6101, 'lng' => 103.6131],
    //         'Banda Aceh' => ['lat' => 5.5483, 'lng' => 95.3238],
    //         'Bengkulu' => ['lat' => -3.8004, 'lng' => 102.2655],
    //         'Lampung' => ['lat' => -4.5585, 'lng' => 105.4068],
    //         'Mataram' => ['lat' => -8.5833, 'lng' => 116.1167],
    //         'Kupang' => ['lat' => -10.1718, 'lng' => 123.6075],
    //         'Ambon' => ['lat' => -3.6954, 'lng' => 128.1814]
    //     ];

    //     // Konversi data untuk map (per kota)
    //     $kotaData = [];
    //     foreach ($kandidatPerKota as $kota) {
    //         $namaKota = $kota->kota_domisili;
    //         if (isset($koordinatKota[$namaKota])) {
    //             $kotaData[] = [
    //                 'nama' => $namaKota,
    //                 'kandidat' => $kota->total,
    //                 'lat' => $koordinatKota[$namaKota]['lat'],
    //                 'lng' => $koordinatKota[$namaKota]['lng']
    //             ];
    //         }
    //     }

    //     return view('Gondowangi.Kandidat.Admin.dashboard', compact(
    //         'totalKaryawan',
    //         'menungguVerifikasi', 
    //         'terverifikasi',
    //         'baruHariIni',
    //         'lowonganAktif',
    //         'monthlyData',
    //         'aktivitasTerbaru',
    //         'kotaData'
    //     ));
    // }
    // public function dashboard()
    // {
    //     // Ambil statistik untuk dashboard
    //     $totalKaryawan = Karyawan::count();
    //     $menungguVerifikasi = Karyawan::where('status', 'pending')->count();
    //     $terverifikasi = Karyawan::where('status', 'approved')->count();
    //     $baruHariIni = Karyawan::whereDate('created_at', today())->count();
        
    //     // Tambahkan data untuk lowongan aktif
    //     $lowonganAktif = CareerPosition::where('status', 'open')->count();

    //     // Data untuk chart bulanan (5 bulan terakhir)
    //     $monthlyData = [];
    //     for ($i = 4; $i >= 0; $i--) {
    //         $date = now()->subMonths($i);
    //         $count = Karyawan::whereYear('created_at', $date->year)
    //             ->whereMonth('created_at', $date->month)
    //             ->count();
    //         $monthlyData[] = [
    //             'month' => $date->format('M'),
    //             'count' => $count
    //         ];
    //     }

    //     // Data aktivitas terbaru (5 data terakhir)
    //     $aktivitasTerbaru = Karyawan::orderBy('created_at', 'desc')
    //         ->take(5)
    //         ->get();

    //     // Data distribusi kandidat per kota (langsung dari database)
    //     $kandidatPerKota = Karyawan::select('kota_domisili', DB::raw('count(*) as total'))
    //         ->whereNotNull('kota_domisili')
    //         ->where('kota_domisili', '!=', '')
    //         ->groupBy('kota_domisili')
    //         ->orderBy('total', 'desc')
    //         ->get();

    //     // Mapping koordinat kota (sesuaikan dengan data yang ada di database)
    //     $koordinatKota = [
    //         'Jakarta' => ['lat' => -6.2088, 'lng' => 106.8456],
    //         'Bandung' => ['lat' => -6.9175, 'lng' => 107.6191],
    //         'Surabaya' => ['lat' => -7.2504, 'lng' => 112.7688],
    //         'Yogyakarta' => ['lat' => -7.7956, 'lng' => 110.3695],
    //         'Medan' => ['lat' => 3.5952, 'lng' => 98.6722],
    //         'Makassar' => ['lat' => -5.1477, 'lng' => 119.4327],
    //         'Denpasar' => ['lat' => -8.6500, 'lng' => 115.2167],
    //         'Palembang' => ['lat' => -2.9761, 'lng' => 104.7754],
    //         'Semarang' => ['lat' => -6.9667, 'lng' => 110.4167],
    //         'Malang' => ['lat' => -7.9797, 'lng' => 112.6304],
    //         'Bekasi' => ['lat' => -6.2383, 'lng' => 106.9756],
    //         'Tangerang' => ['lat' => -6.1783, 'lng' => 106.6319],
    //         'KABUPATEN TANGERANG' => ['lat' => -6.1783, 'lng' => 106.6319],
    //         'Bogor' => ['lat' => -6.5950, 'lng' => 106.7966],
    //         'DEPOK, JAWA BARAT' => ['lat' => -6.4025, 'lng' => 106.7942],
    //         'Batam' => ['lat' => 1.1304, 'lng' => 104.0530],
    //         'Pekanbaru' => ['lat' => 0.5071, 'lng' => 101.4478],
    //         'Banjarmasin' => ['lat' => -3.3194, 'lng' => 114.5906],
    //         'Samarinda' => ['lat' => -0.4978, 'lng' => 117.1436],
    //         'Manado' => ['lat' => 1.4748, 'lng' => 124.8421],
    //         'Jayapura' => ['lat' => -2.5489, 'lng' => 140.7197],
    //         'Pontianak' => ['lat' => -0.0263, 'lng' => 109.3425],
    //         'Balikpapan' => ['lat' => -1.2379, 'lng' => 116.8289],
    //         'Padang' => ['lat' => -0.9471, 'lng' => 100.4172],
    //         'Jambi' => ['lat' => -1.6101, 'lng' => 103.6131],
    //         'Banda Aceh' => ['lat' => 5.5483, 'lng' => 95.3238],
    //         'Bengkulu' => ['lat' => -3.8004, 'lng' => 102.2655],
    //         'Lampung' => ['lat' => -4.5585, 'lng' => 105.4068],
    //         'Mataram' => ['lat' => -8.5833, 'lng' => 116.1167],
    //         'Kupang' => ['lat' => -10.1718, 'lng' => 123.6075],
    //         'Ambon' => ['lat' => -3.6954, 'lng' => 128.1814]
    //     ];

    //     // Konversi data untuk map (per kota)
    //     $kotaData = [];
    //     foreach ($kandidatPerKota as $kota) {
    //         $namaKota = $kota->kota_domisili;
    //         if (isset($koordinatKota[$namaKota])) {
    //             $kotaData[] = [
    //                 'nama' => $namaKota,
    //                 'kandidat' => $kota->total,
    //                 'lat' => $koordinatKota[$namaKota]['lat'],
    //                 'lng' => $koordinatKota[$namaKota]['lng']
    //             ];
    //         }
    //     }
        
    //     $lowonganList = CareerPosition::where('status', 'open')
    //         ->orderBy('position_title')
    //         ->get(['id', 'position_title']);

    //     return view('Gondowangi.Kandidat.Admin.dashboard', compact(
    //         'totalKaryawan',
    //         'menungguVerifikasi', 
    //         'terverifikasi',
    //         'baruHariIni',
    //         'lowonganAktif',
    //         'monthlyData',
    //         'aktivitasTerbaru',
    //         'kotaData',
    //         'lowonganList'
    //     ));
    // }
    
    public function dashboard()
    {
        // Ambil statistik untuk dashboard
        $totalKaryawan = Karyawan::count();
        $menungguVerifikasi = Karyawan::where('status', 'pending')->count();
        $terverifikasi = Karyawan::where('status', 'approved')->count();
        $baruHariIni = Karyawan::whereDate('created_at', today())->count();
        
        // Tambahkan data untuk lowongan aktif
        $lowonganAktif = CareerPosition::where('status', 'open')->count();

        // Data untuk chart bulanan (5 bulan terakhir)
        $monthlyData = [];
        for ($i = 4; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Karyawan::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $monthlyData[] = [
                'month' => $date->format('M'),
                'count' => $count
            ];
        }

        // Data aktivitas terbaru (5 data terakhir)
        $aktivitasTerbaru = Karyawan::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Data distribusi kandidat per kota (langsung dari database)
        $kandidatPerKota = Karyawan::select('kota_domisili', DB::raw('count(*) as total'))
            ->whereNotNull('kota_domisili')
            ->where('kota_domisili', '!=', '')
            ->groupBy('kota_domisili')
            ->orderBy('total', 'desc')
            ->get();

        // Mapping koordinat kota (sesuaikan dengan data yang ada di database)
        $koordinatKota = [
            // DKI Jakarta
            'Jakarta' => ['lat' => -6.2088, 'lng' => 106.8456],
            'Jakarta Pusat' => ['lat' => -6.1745, 'lng' => 106.8227],
            'Jakarta Utara' => ['lat' => -6.1384, 'lng' => 106.8631],
            'Jakarta Barat' => ['lat' => -6.1666, 'lng' => 106.7593],
            'Jakarta Selatan' => ['lat' => -6.2615, 'lng' => 106.8106],
            'Jakarta Timur' => ['lat' => -6.2250, 'lng' => 106.9004],
            'Kepulauan Seribu' => ['lat' => -5.6667, 'lng' => 106.5833],
        
            // Jawa Barat
            'Bandung' => ['lat' => -6.9175, 'lng' => 107.6191],
            'Bekasi' => ['lat' => -6.2383, 'lng' => 106.9756],
            'Bogor' => ['lat' => -6.5950, 'lng' => 106.7966],
            'Cimahi' => ['lat' => -6.8667, 'lng' => 107.5333],
            'Cirebon' => ['lat' => -6.7063, 'lng' => 108.5571],
            'Depok' => ['lat' => -6.4025, 'lng' => 106.7942],
            'Sukabumi' => ['lat' => -6.9178, 'lng' => 106.9269],
            'Tasikmalaya' => ['lat' => -7.3506, 'lng' => 108.2111],
            'Banjar' => ['lat' => -7.3667, 'lng' => 108.5333],
            'Kabupaten Bandung' => ['lat' => -7.0167, 'lng' => 107.5000],
            'Kabupaten Bandung Barat' => ['lat' => -6.8667, 'lng' => 107.4833],
            'Kabupaten Bekasi' => ['lat' => -6.2667, 'lng' => 107.1333],
            'Kabupaten Bogor' => ['lat' => -6.6000, 'lng' => 106.8000],
            'Kabupaten Ciamis' => ['lat' => -7.3333, 'lng' => 108.3500],
            'Kabupaten Cianjur' => ['lat' => -6.8167, 'lng' => 107.1333],
            'Kabupaten Cirebon' => ['lat' => -6.7333, 'lng' => 108.4167],
            'Kabupaten Garut' => ['lat' => -7.2167, 'lng' => 107.9000],
            'Kabupaten Indramayu' => ['lat' => -6.3333, 'lng' => 108.3333],
            'Kabupaten Karawang' => ['lat' => -6.3000, 'lng' => 107.3000],
            'Kabupaten Kuningan' => ['lat' => -6.9833, 'lng' => 108.4833],
            'Kabupaten Majalengka' => ['lat' => -6.8333, 'lng' => 108.2333],
            'Kabupaten Pangandaran' => ['lat' => -7.6833, 'lng' => 108.6500],
            'Kabupaten Purwakarta' => ['lat' => -6.5667, 'lng' => 107.4333],
            'Kabupaten Subang' => ['lat' => -6.5667, 'lng' => 107.7500],
            'Kabupaten Sukabumi' => ['lat' => -6.9333, 'lng' => 106.9333],
            'Kabupaten Sumedang' => ['lat' => -6.8667, 'lng' => 107.9167],
            'Kabupaten Tasikmalaya' => ['lat' => -7.3333, 'lng' => 108.2167],
        
            // Jawa Tengah
            'Semarang' => ['lat' => -6.9667, 'lng' => 110.4167],
            'Magelang' => ['lat' => -7.4667, 'lng' => 110.2167],
            'Pekalongan' => ['lat' => -6.8833, 'lng' => 109.6667],
            'Salatiga' => ['lat' => -7.3333, 'lng' => 110.5000],
            'Surakarta' => ['lat' => -7.5667, 'lng' => 110.8333],
            'Tegal' => ['lat' => -6.8667, 'lng' => 109.1333],
            'Kabupaten Banjarnegara' => ['lat' => -7.3167, 'lng' => 109.6833],
            'Kabupaten Banyumas' => ['lat' => -7.5167, 'lng' => 109.2833],
            'Kabupaten Batang' => ['lat' => -6.9167, 'lng' => 109.7333],
            'Kabupaten Blora' => ['lat' => -6.9667, 'lng' => 111.4167],
            'Kabupaten Boyolali' => ['lat' => -7.5333, 'lng' => 110.5833],
            'Kabupaten Brebes' => ['lat' => -6.8667, 'lng' => 109.0333],
            'Kabupaten Cilacap' => ['lat' => -7.7167, 'lng' => 109.0167],
            'Kabupaten Demak' => ['lat' => -6.8833, 'lng' => 110.6333],
            'Kabupaten Grobogan' => ['lat' => -7.0667, 'lng' => 110.9167],
            'Kabupaten Jepara' => ['lat' => -6.5833, 'lng' => 110.6667],
            'Kabupaten Karanganyar' => ['lat' => -7.6167, 'lng' => 111.0333],
            'Kabupaten Kebumen' => ['lat' => -7.6667, 'lng' => 109.6667],
            'Kabupaten Kendal' => ['lat' => -6.9167, 'lng' => 110.2000],
            'Kabupaten Klaten' => ['lat' => -7.7167, 'lng' => 110.6000],
            'Kabupaten Kudus' => ['lat' => -6.8167, 'lng' => 110.8333],
            'Kabupaten Magelang' => ['lat' => -7.4667, 'lng' => 110.2167],
            'Kabupaten Pati' => ['lat' => -6.7500, 'lng' => 111.0333],
            'Kabupaten Pekalongan' => ['lat' => -7.0000, 'lng' => 109.6167],
            'Kabupaten Pemalang' => ['lat' => -6.8833, 'lng' => 109.3833],
            'Kabupaten Purbalingga' => ['lat' => -7.3833, 'lng' => 109.3667],
            'Kabupaten Purworejo' => ['lat' => -7.7167, 'lng' => 110.0167],
            'Kabupaten Rembang' => ['lat' => -6.7000, 'lng' => 111.3500],
            'Kabupaten Semarang' => ['lat' => -7.1500, 'lng' => 110.5000],
            'Kabupaten Sragen' => ['lat' => -7.4167, 'lng' => 111.0000],
            'Kabupaten Sukoharjo' => ['lat' => -7.6833, 'lng' => 110.8333],
            'Kabupaten Tegal' => ['lat' => -6.9167, 'lng' => 109.1000],
            'Kabupaten Temanggung' => ['lat' => -7.3167, 'lng' => 110.1667],
            'Kabupaten Wonogiri' => ['lat' => -7.8167, 'lng' => 110.9167],
            'Kabupaten Wonosobo' => ['lat' => -7.3667, 'lng' => 109.9000],
        
            // DI Yogyakarta
            'Yogyakarta' => ['lat' => -7.7956, 'lng' => 110.3695],
            'Kabupaten Bantul' => ['lat' => -7.8833, 'lng' => 110.3333],
            'Kabupaten Gunungkidul' => ['lat' => -7.9667, 'lng' => 110.6000],
            'Kabupaten Kulon Progo' => ['lat' => -7.8333, 'lng' => 110.1667],
            'Kabupaten Sleman' => ['lat' => -7.7167, 'lng' => 110.3500],
        
            // Jawa Timur
            'Surabaya' => ['lat' => -7.2504, 'lng' => 112.7688],
            'Malang' => ['lat' => -7.9797, 'lng' => 112.6304],
            'Batu' => ['lat' => -7.8667, 'lng' => 112.5167],
            'Blitar' => ['lat' => -8.0667, 'lng' => 112.1667],
            'Kediri' => ['lat' => -7.8167, 'lng' => 112.0167],
            'Madiun' => ['lat' => -7.6333, 'lng' => 111.5333],
            'Mojokerto' => ['lat' => -7.4667, 'lng' => 112.4333],
            'Pasuruan' => ['lat' => -7.6333, 'lng' => 112.9000],
            'Probolinggo' => ['lat' => -7.7500, 'lng' => 113.2167],
            'Kabupaten Bangkalan' => ['lat' => -7.0333, 'lng' => 112.7500],
            'Kabupaten Banyuwangi' => ['lat' => -8.2167, 'lng' => 114.3667],
            'Kabupaten Blitar' => ['lat' => -8.1000, 'lng' => 112.1667],
            'Kabupaten Bojonegoro' => ['lat' => -7.1500, 'lng' => 111.8833],
            'Kabupaten Bondowoso' => ['lat' => -7.9167, 'lng' => 113.8167],
            'Kabupaten Gresik' => ['lat' => -7.1667, 'lng' => 112.6167],
            'Kabupaten Jember' => ['lat' => -8.1667, 'lng' => 113.7000],
            'Kabupaten Jombang' => ['lat' => -7.5500, 'lng' => 112.2333],
            'Kabupaten Kediri' => ['lat' => -7.8333, 'lng' => 112.0167],
            'Kabupaten Lamongan' => ['lat' => -7.1167, 'lng' => 112.4167],
            'Kabupaten Lumajang' => ['lat' => -8.1333, 'lng' => 113.2167],
            'Kabupaten Madiun' => ['lat' => -7.6333, 'lng' => 111.5000],
            'Kabupaten Magetan' => ['lat' => -7.6500, 'lng' => 111.3500],
            'Kabupaten Malang' => ['lat' => -8.0167, 'lng' => 112.6333],
            'Kabupaten Mojokerto' => ['lat' => -7.4667, 'lng' => 112.4333],
            'Kabupaten Nganjuk' => ['lat' => -7.6000, 'lng' => 111.9000],
            'Kabupaten Ngawi' => ['lat' => -7.4000, 'lng' => 111.4500],
            'Kabupaten Pacitan' => ['lat' => -8.2000, 'lng' => 111.0833],
            'Kabupaten Pamekasan' => ['lat' => -7.1667, 'lng' => 113.4833],
            'Kabupaten Pasuruan' => ['lat' => -7.7333, 'lng' => 112.9000],
            'Kabupaten Ponorogo' => ['lat' => -7.8667, 'lng' => 111.4667],
            'Kabupaten Probolinggo' => ['lat' => -7.8833, 'lng' => 113.2167],
            'Kabupaten Sampang' => ['lat' => -7.1833, 'lng' => 113.2333],
            'Kabupaten Sidoarjo' => ['lat' => -7.4500, 'lng' => 112.7167],
            'Kabupaten Situbondo' => ['lat' => -7.7167, 'lng' => 114.0167],
            'Kabupaten Sumenep' => ['lat' => -7.0167, 'lng' => 113.8667],
            'Kabupaten Trenggalek' => ['lat' => -8.0500, 'lng' => 111.7167],
            'Kabupaten Tuban' => ['lat' => -6.9000, 'lng' => 111.9667],
            'Kabupaten Tulungagung' => ['lat' => -8.0667, 'lng' => 111.9000],
        
            // Banten
            'Tangerang' => ['lat' => -6.1783, 'lng' => 106.6319],
            'Tangerang Selatan' => ['lat' => -6.2875, 'lng' => 106.7175],
            'Cilegon' => ['lat' => -6.0167, 'lng' => 106.0333],
            'Serang' => ['lat' => -6.1167, 'lng' => 106.1500],
            'Kabupaten Lebak' => ['lat' => -6.5667, 'lng' => 106.2500],
            'Kabupaten Pandeglang' => ['lat' => -6.3167, 'lng' => 106.1000],
            'Kabupaten Serang' => ['lat' => -6.1167, 'lng' => 106.1500],
            'Kabupaten Tangerang' => ['lat' => -6.1783, 'lng' => 106.6319],
        
            // Sumatera Utara
            'Medan' => ['lat' => 3.5952, 'lng' => 98.6722],
            'Binjai' => ['lat' => 3.6000, 'lng' => 98.4833],
            'Gunungsitoli' => ['lat' => 1.2833, 'lng' => 97.6167],
            'Padangsidimpuan' => ['lat' => 1.3833, 'lng' => 99.2667],
            'Pematangsiantar' => ['lat' => 2.9667, 'lng' => 99.0667],
            'Sibolga' => ['lat' => 1.7333, 'lng' => 98.7833],
            'Tanjungbalai' => ['lat' => 2.9667, 'lng' => 99.8000],
            'Tebing Tinggi' => ['lat' => 3.3167, 'lng' => 99.1500],
            'Kabupaten Asahan' => ['lat' => 2.9833, 'lng' => 99.6167],
            'Kabupaten Batu Bara' => ['lat' => 3.2167, 'lng' => 99.4833],
            'Kabupaten Dairi' => ['lat' => 2.8667, 'lng' => 98.2000],
            'Kabupaten Deli Serdang' => ['lat' => 3.4333, 'lng' => 98.6667],
            'Kabupaten Humbang Hasundutan' => ['lat' => 2.2000, 'lng' => 98.5000],
            'Kabupaten Karo' => ['lat' => 3.1333, 'lng' => 98.5000],
            'Kabupaten Labuhanbatu' => ['lat' => 2.1667, 'lng' => 100.1167],
            'Kabupaten Labuhanbatu Selatan' => ['lat' => 1.8500, 'lng' => 100.1000],
            'Kabupaten Labuhanbatu Utara' => ['lat' => 2.4167, 'lng' => 100.0833],
            'Kabupaten Langkat' => ['lat' => 3.8000, 'lng' => 98.4167],
            'Kabupaten Mandailing Natal' => ['lat' => 0.7833, 'lng' => 99.3500],
            'Kabupaten Nias' => ['lat' => 1.0833, 'lng' => 97.5833],
            'Kabupaten Nias Barat' => ['lat' => 1.0667, 'lng' => 97.2167],
            'Kabupaten Nias Selatan' => ['lat' => 0.6333, 'lng' => 97.7833],
            'Kabupaten Nias Utara' => ['lat' => 1.4167, 'lng' => 97.1667],
            'Kabupaten Padang Lawas' => ['lat' => 1.7167, 'lng' => 99.8333],
            'Kabupaten Padang Lawas Utara' => ['lat' => 2.1000, 'lng' => 99.6667],
            'Kabupaten Pakpak Bharat' => ['lat' => 2.6167, 'lng' => 98.2500],
            'Kabupaten Samosir' => ['lat' => 2.6667, 'lng' => 98.7000],
            'Kabupaten Serdang Bedagai' => ['lat' => 3.3333, 'lng' => 99.1167],
            'Kabupaten Simalungun' => ['lat' => 2.9667, 'lng' => 99.0167],
            'Kabupaten Tapanuli Selatan' => ['lat' => 1.5500, 'lng' => 99.2667],
            'Kabupaten Tapanuli Tengah' => ['lat' => 1.9333, 'lng' => 98.9833],
            'Kabupaten Tapanuli Utara' => ['lat' => 2.0333, 'lng' => 99.0667],
            'Kabupaten Toba' => ['lat' => 2.3833, 'lng' => 99.0667],
        
            // Sumatera Barat
            'Padang' => ['lat' => -0.9471, 'lng' => 100.4172],
            'Bukittinggi' => ['lat' => -0.3000, 'lng' => 100.3667],
            'Padangpanjang' => ['lat' => -0.4667, 'lng' => 100.4000],
            'Pariaman' => ['lat' => -0.6167, 'lng' => 100.1167],
            'Payakumbuh' => ['lat' => -0.2167, 'lng' => 100.6333],
            'Sawahlunto' => ['lat' => -0.6833, 'lng' => 100.7833],
            'Solok' => ['lat' => -0.8000, 'lng' => 100.6500],
            'Kabupaten Agam' => ['lat' => -0.2333, 'lng' => 100.3167],
            'Kabupaten Dharmasraya' => ['lat' => -1.0833, 'lng' => 101.3500],
            'Kabupaten Kepulauan Mentawai' => ['lat' => -2.0833, 'lng' => 99.6500],
            'Kabupaten Lima Puluh Kota' => ['lat' => -0.0500, 'lng' => 100.5167],
            'Kabupaten Padang Pariaman' => ['lat' => -0.5667, 'lng' => 100.1833],
            'Kabupaten Pasaman' => ['lat' => 0.2833, 'lng' => 100.0333],
            'Kabupaten Pasaman Barat' => ['lat' => 0.0833, 'lng' => 99.6333],
            'Kabupaten Pesisir Selatan' => ['lat' => -1.7667, 'lng' => 100.9667],
            'Kabupaten Sijunjung' => ['lat' => -0.6833, 'lng' => 101.0167],
            'Kabupaten Solok' => ['lat' => -1.0333, 'lng' => 100.9833],
            'Kabupaten Solok Selatan' => ['lat' => -1.4833, 'lng' => 101.1667],
            'Kabupaten Tanah Datar' => ['lat' => -0.4833, 'lng' => 100.5167],
        
            // Riau
            'Pekanbaru' => ['lat' => 0.5071, 'lng' => 101.4478],
            'Dumai' => ['lat' => 1.6833, 'lng' => 101.4500],
            'Kabupaten Bengkalis' => ['lat' => 1.4667, 'lng' => 102.1167],
            'Kabupaten Indragiri Hilir' => ['lat' => -0.3167, 'lng' => 103.2167],
            'Kabupaten Indragiri Hulu' => ['lat' => -0.5500, 'lng' => 102.1167],
            'Kabupaten Kampar' => ['lat' => 0.3333, 'lng' => 101.1500],
            'Kabupaten Kepulauan Meranti' => ['lat' => 1.2333, 'lng' => 103.6167],
            'Kabupaten Kuantan Singingi' => ['lat' => -0.4833, 'lng' => 101.4667],
            'Kabupaten Pelalawan' => ['lat' => 0.3500, 'lng' => 102.2333],
            'Kabupaten Rokan Hilir' => ['lat' => 2.0833, 'lng' => 100.8833],
            'Kabupaten Rokan Hulu' => ['lat' => 1.1333, 'lng' => 100.4667],
            'Kabupaten Siak' => ['lat' => 1.1167, 'lng' => 101.8167],
        
            // Kepulauan Riau
            'Batam' => ['lat' => 1.1304, 'lng' => 104.0530],
            'Tanjungpinang' => ['lat' => 0.9167, 'lng' => 104.4500],
            'Kabupaten Bintan' => ['lat' => 1.1167, 'lng' => 104.6000],
            'Kabupaten Karimun' => ['lat' => 1.0500, 'lng' => 103.4167],
            'Kabupaten Kepulauan Anambas' => ['lat' => 3.0167, 'lng' => 106.0667],
            'Kabupaten Lingga' => ['lat' => -0.2000, 'lng' => 104.6167],
            'Kabupaten Natuna' => ['lat' => 4.0000, 'lng' => 108.2500],
        
            // Jambi
            'Jambi' => ['lat' => -1.6101, 'lng' => 103.6131],
            'Sungai Penuh' => ['lat' => -2.0667, 'lng' => 101.3833],
            'Kabupaten Batanghari' => ['lat' => -1.7833, 'lng' => 103.1167],
            'Kabupaten Bungo' => ['lat' => -1.4833, 'lng' => 101.8833],
            'Kabupaten Kerinci' => ['lat' => -1.9667, 'lng' => 101.0833],
            'Kabupaten Merangin' => ['lat' => -2.1167, 'lng' => 101.9833],
            'Kabupaten Muaro Jambi' => ['lat' => -1.4833, 'lng' => 103.8167],
            'Kabupaten Sarolangun' => ['lat' => -2.2833, 'lng' => 102.6167],
            'Kabupaten Tanjung Jabung Barat' => ['lat' => -1.0833, 'lng' => 103.0333],
            'Kabupaten Tanjung Jabung Timur' => ['lat' => -1.2167, 'lng' => 103.8667],
            'Kabupaten Tebo' => ['lat' => -1.4167, 'lng' => 102.4167],
        
            // Sumatera Selatan
            'Palembang' => ['lat' => -2.9761, 'lng' => 104.7754],
            'Lubuklinggau' => ['lat' => -3.3000, 'lng' => 102.8667],
            'Pagar Alam' => ['lat' => -4.0667, 'lng' => 103.2333],
            'Prabumulih' => ['lat' => -3.4333, 'lng' => 104.2333],
            'Kabupaten Banyuasin' => ['lat' => -2.7667, 'lng' => 104.8833],
            'Kabupaten Empat Lawang' => ['lat' => -3.8333, 'lng' => 102.9833],
            'Kabupaten Lahat' => ['lat' => -3.7833, 'lng' => 103.5167],
            'Kabupaten Muara Enim' => ['lat' => -3.7000, 'lng' => 103.9333],
            'Kabupaten Musi Banyuasin' => ['lat' => -2.5833, 'lng' => 104.2833],
            'Kabupaten Musi Rawas' => ['lat' => -2.9500, 'lng' => 102.6667],
            'Kabupaten Musi Rawas Utara' => ['lat' => -2.4833, 'lng' => 102.1333],
            'Kabupaten Ogan Ilir' => ['lat' => -3.4167, 'lng' => 104.5833],
            'Kabupaten Ogan Komering Ilir' => ['lat' => -3.2167, 'lng' => 105.1833],
            'Kabupaten Ogan Komering Ulu' => ['lat' => -4.1667, 'lng' => 103.9167],
            'Kabupaten Ogan Komering Ulu Selatan' => ['lat' => -4.8333, 'lng' => 103.9667],
            'Kabupaten Ogan Komering Ulu Timur' => ['lat' => -4.3833, 'lng' => 104.4167],
        
            // Bengkulu
            'Bengkulu' => ['lat' => -3.8004, 'lng' => 102.2655],
            'Kabupaten Bengkulu Selatan' => ['lat' => -4.4167, 'lng' => 102.9333],
            'Kabupaten Bengkulu Tengah' => ['lat' => -3.5833, 'lng' => 102.5833],
            'Kabupaten Bengkulu Utara' => ['lat' => -3.3167, 'lng' => 101.9333],
            'Kabupaten Kaur' => ['lat' => -4.6167, 'lng' => 103.4167],
            'Kabupaten Kepahiang' => ['lat' => -3.6167, 'lng' => 102.5833],
            'Kabupaten Lebong' => ['lat' => -3.2000, 'lng' => 102.1833],
            'Kabupaten Mukomuko' => ['lat' => -2.5833, 'lng' => 101.0833],
            'Kabupaten Rejang Lebong' => ['lat' => -3.4333, 'lng' => 102.1500],
            'Kabupaten Seluma' => ['lat' => -4.0667, 'lng' => 102.4833],
        
            // Lampung
            'Bandar Lampung' => ['lat' => -5.4292, 'lng' => 105.2610],
            'Metro' => ['lat' => -5.1133, 'lng' => 105.3067],
            'Kabupaten Lampung Barat' => ['lat' => -5.0667, 'lng' => 104.2833],
            'Kabupaten Lampung Selatan' => ['lat' => -5.6333, 'lng' => 105.5167],
            'Kabupaten Lampung Tengah' => ['lat' => -4.8667, 'lng' => 105.2833],
            'Kabupaten Lampung Timur' => ['lat' => -4.8333, 'lng' => 105.6500],
            'Kabupaten Lampung Utara' => ['lat' => -4.3333, 'lng' => 104.8833],
            'Kabupaten Mesuji' => ['lat' => -3.4167, 'lng' => 105.8167],
            'Kabupaten Pesawaran' => ['lat' => -5.3667, 'lng' => 105.0500],
            'Kabupaten Pesisir Barat' => ['lat' => -5.0833, 'lng' => 103.9167],
            'Kabupaten Pringsewu' => ['lat' => -5.3500, 'lng' => 104.9667],
            'Kabupaten Tanggamus' => ['lat' => -5.4833, 'lng' => 104.6333],
            'Kabupaten Tulang Bawang' => ['lat' => -4.0333, 'lng' => 105.4500],
            'Kabupaten Tulang Bawang Barat' => ['lat' => -4.4167, 'lng' => 105.1833],
            'Kabupaten Way Kanan' => ['lat' => -4.2333, 'lng' => 104.5833],
        
            // Bangka Belitung
            'Pangkalpinang' => ['lat' => -2.1333, 'lng' => 106.1167],
            'Kabupaten Bangka' => ['lat' => -2.0667, 'lng' => 106.0667],
            'Kabupaten Bangka Barat' => ['lat' => -1.8500, 'lng' => 105.9167],
            'Kabupaten Bangka Selatan' => ['lat' => -2.8500, 'lng' => 106.7333],
            'Kabupaten Bangka Tengah' => ['lat' => -2.4833, 'lng' => 106.2167],
            'Kabupaten Belitung' => ['lat' => -2.7667, 'lng' => 107.6333],
            'Kabupaten Belitung Timur' => ['lat' => -2.8167, 'lng' => 108.0333],
        
            // Aceh
            'Banda Aceh' => ['lat' => 5.5483, 'lng' => 95.3238],
            'Langsa' => ['lat' => 4.4667, 'lng' => 97.9667],
            'Lhokseumawe' => ['lat' => 5.1833, 'lng' => 97.1500],
            'Sabang' => ['lat' => 5.8833, 'lng' => 95.3167],
            'Subulussalam' => ['lat' => 2.6833, 'lng' => 97.9500],
            'Kabupaten Aceh Barat' => ['lat' => 4.4167, 'lng' => 96.1500],
            'Kabupaten Aceh Barat Daya' => ['lat' => 3.7833, 'lng' => 96.8167],
            'Kabupaten Aceh Besar' => ['lat' => 5.4167, 'lng' => 95.5167],
            'Kabupaten Aceh Jaya' => ['lat' => 4.8167, 'lng' => 95.6500],
            'Kabupaten Aceh Selatan' => ['lat' => 3.2167, 'lng' => 97.3667],
            'Kabupaten Aceh Singkil' => ['lat' => 2.4167, 'lng' => 97.9667],
            'Kabupaten Aceh Tamiang' => ['lat' => 4.2667, 'lng' => 98.0167],
            'Kabupaten Aceh Tengah' => ['lat' => 4.6333, 'lng' => 96.8167],
            'Kabupaten Aceh Tenggara' => ['lat' => 3.8833, 'lng' => 97.5333],
            'Kabupaten Aceh Timur' => ['lat' => 4.6333, 'lng' => 97.6167],
            'Kabupaten Aceh Utara' => ['lat' => 5.1167, 'lng' => 97.1333],
            'Kabupaten Bener Meriah' => ['lat' => 4.7833, 'lng' => 96.6333],
            'Kabupaten Bireuen' => ['lat' => 5.2000, 'lng' => 96.7000],
            'Kabupaten Gayo Lues' => ['lat' => 4.3167, 'lng' => 97.4167],
            'Kabupaten Nagan Raya' => ['lat' => 4.1333, 'lng' => 96.5167],
            'Kabupaten Pidie' => ['lat' => 5.1333, 'lng' => 95.9833],
            'Kabupaten Pidie Jaya' => ['lat' => 5.1500, 'lng' => 96.1833],
            'Kabupaten Simeulue' => ['lat' => 2.6167, 'lng' => 96.0833],
        
            // Kalimantan Barat
            'Pontianak' => ['lat' => -0.0263, 'lng' => 109.3425],
            'Singkawang' => ['lat' => 0.9167, 'lng' => 108.9833],
            'Kabupaten Bengkayang' => ['lat' => 1.0500, 'lng' => 109.4167],
            'Kabupaten Kapuas Hulu' => ['lat' => 0.8167, 'lng' => 112.0000],
            'Kabupaten Kayong Utara' => ['lat' => -0.6167, 'lng' => 109.6667],
            'Kabupaten Ketapang' => ['lat' => -1.8333, 'lng' => 109.9833],
            'Kabupaten Kubu Raya' => ['lat' => -0.1167, 'lng' => 109.4833],
            'Kabupaten Landak' => ['lat' => -0.9167, 'lng' => 109.1833],
            'Kabupaten Melawi' => ['lat' => -0.2000, 'lng' => 111.3500],
            'Kabupaten Mempawah' => ['lat' => -0.3167, 'lng' => 109.1833],
            'Kabupaten Sambas' => ['lat' => 1.3667, 'lng' => 109.3000],
            'Kabupaten Sanggau' => ['lat' => 0.1667, 'lng' => 110.3333],
            'Kabupaten Sekadau' => ['lat' => 0.0833, 'lng' => 110.9167],
            'Kabupaten Sintang' => ['lat' => 0.1167, 'lng' => 111.5000],
        
            // Kalimantan Tengah
            'Palangkaraya' => ['lat' => -2.2135, 'lng' => 113.9213],
            'Kabupaten Barito Selatan' => ['lat' => -2.6167, 'lng' => 114.7000],
            'Kabupaten Barito Timur' => ['lat' => -1.9333, 'lng' => 114.6500],
            'Kabupaten Barito Utara' => ['lat' => -1.4833, 'lng' => 114.7833],
            'Kabupaten Gunung Mas' => ['lat' => -1.0667, 'lng' => 113.4167],
            'Kabupaten Kapuas' => ['lat' => -1.7500, 'lng' => 114.3833],
            'Kabupaten Katingan' => ['lat' => -1.6833, 'lng' => 112.9833],
            'Kabupaten Kotawaringin Barat' => ['lat' => -2.6667, 'lng' => 111.6167],
            'Kabupaten Kotawaringin Timur' => ['lat' => -2.7500, 'lng' => 112.9167],
            'Kabupaten Lamandau' => ['lat' => -2.5167, 'lng' => 111.3333],
            'Kabupaten Murung Raya' => ['lat' => -0.6833, 'lng' => 114.2833],
            'Kabupaten Pulang Pisau' => ['lat' => -2.6167, 'lng' => 114.0167],
            'Kabupaten Seruyan' => ['lat' => -2.2500, 'lng' => 112.4667],
            'Kabupaten Sukamara' => ['lat' => -2.6667, 'lng' => 111.2500],
        
            // Kalimantan Selatan
            'Banjarmasin' => ['lat' => -3.3194, 'lng' => 114.5906],
            'Banjarbaru' => ['lat' => -3.4167, 'lng' => 114.8333],
            'Kabupaten Balangan' => ['lat' => -2.2833, 'lng' => 115.6500],
            'Kabupaten Banjar' => ['lat' => -3.3833, 'lng' => 114.8167],
            'Kabupaten Barito Kuala' => ['lat' => -3.2833, 'lng' => 114.6500],
            'Kabupaten Hulu Sungai Selatan' => ['lat' => -2.7167, 'lng' => 115.2167],
            'Kabupaten Hulu Sungai Tengah' => ['lat' => -2.6000, 'lng' => 115.4167],
            'Kabupaten Hulu Sungai Utara' => ['lat' => -2.5000, 'lng' => 115.1833],
            'Kabupaten Kotabaru' => ['lat' => -3.3000, 'lng' => 116.1667],
            'Kabupaten Tabalong' => ['lat' => -2.0167, 'lng' => 115.4833],
            'Kabupaten Tanah Bumbu' => ['lat' => -3.4333, 'lng' => 115.6500],
            'Kabupaten Tanah Laut' => ['lat' => -3.8000, 'lng' => 114.8500],
            'Kabupaten Tapin' => ['lat' => -2.9167, 'lng' => 115.1833],
        
            // Kalimantan Timur
            'Samarinda' => ['lat' => -0.4978, 'lng' => 117.1436],
            'Balikpapan' => ['lat' => -1.2379, 'lng' => 116.8289],
            'Bontang' => ['lat' => 0.1333, 'lng' => 117.4833],
            'Kabupaten Berau' => ['lat' => 2.0000, 'lng' => 117.3833],
            'Kabupaten Kutai Barat' => ['lat' => -0.0333, 'lng' => 115.2833],
            'Kabupaten Kutai Kartanegara' => ['lat' => -0.5833, 'lng' => 116.9833],
            'Kabupaten Kutai Timur' => ['lat' => 0.5167, 'lng' => 117.4833],
            'Kabupaten Mahakam Ulu' => ['lat' => 0.6500, 'lng' => 115.1167],
            'Kabupaten Paser' => ['lat' => -1.7000, 'lng' => 116.1167],
            'Kabupaten Penajam Paser Utara' => ['lat' => -1.0833, 'lng' => 116.6167],
        
            // Kalimantan Utara
            'Tarakan' => ['lat' => 3.3000, 'lng' => 117.6333],
            'Kabupaten Bulungan' => ['lat' => 2.9333, 'lng' => 117.3667],
            'Kabupaten Malinau' => ['lat' => 3.6000, 'lng' => 116.0333],
            'Kabupaten Nunukan' => ['lat' => 4.0833, 'lng' => 117.0833],
            'Kabupaten Tana Tidung' => ['lat' => 3.4667, 'lng' => 117.2333],
        
            // Sulawesi Utara
            'Manado' => ['lat' => 1.4748, 'lng' => 124.8421],
            'Bitung' => ['lat' => 1.4500, 'lng' => 125.1833],
            'Kotamobagu' => ['lat' => 0.7167, 'lng' => 124.3167],
            'Tomohon' => ['lat' => 1.3333, 'lng' => 124.8333],
            'Kabupaten Bolaang Mongondow' => ['lat' => 0.7167, 'lng' => 124.0833],
            'Kabupaten Bolaang Mongondow Selatan' => ['lat' => 0.4167, 'lng' => 123.8333],
            'Kabupaten Bolaang Mongondow Timur' => ['lat' => 0.6167, 'lng' => 124.5167],
            'Kabupaten Bolaang Mongondow Utara' => ['lat' => 0.8833, 'lng' => 124.0833],
            'Kabupaten Kepulauan Sangihe' => ['lat' => 3.5833, 'lng' => 125.5000],
            'Kabupaten Kepulauan Siau Tagulandang Biaro' => ['lat' => 2.7500, 'lng' => 125.4000],
            'Kabupaten Kepulauan Talaud' => ['lat' => 4.2833, 'lng' => 126.7833],
            'Kabupaten Minahasa' => ['lat' => 1.3000, 'lng' => 124.9000],
            'Kabupaten Minahasa Selatan' => ['lat' => 1.0667, 'lng' => 124.4333],
            'Kabupaten Minahasa Tenggara' => ['lat' => 0.9833, 'lng' => 124.7833],
            'Kabupaten Minahasa Utara' => ['lat' => 1.5167, 'lng' => 125.0833],
        
            // Sulawesi Tengah
            'Palu' => ['lat' => -0.8917, 'lng' => 119.8707],
            'Kabupaten Banggai' => ['lat' => -1.5500, 'lng' => 123.0167],
            'Kabupaten Banggai Kepulauan' => ['lat' => -1.6333, 'lng' => 123.4500],
            'Kabupaten Banggai Laut' => ['lat' => -1.7833, 'lng' => 124.7667],
            'Kabupaten Buol' => ['lat' => 1.1000, 'lng' => 121.4167],
            'Kabupaten Donggala' => ['lat' => -0.4333, 'lng' => 119.7667],
            'Kabupaten Morowali' => ['lat' => -2.4167, 'lng' => 121.8833],
            'Kabupaten Morowali Utara' => ['lat' => -1.8000, 'lng' => 121.3667],
            'Kabupaten Parigi Moutong' => ['lat' => -0.6333, 'lng' => 120.7000],
            'Kabupaten Poso' => ['lat' => -1.3833, 'lng' => 120.7500],
            'Kabupaten Sigi' => ['lat' => -1.3000, 'lng' => 119.8833],
            'Kabupaten Tojo Una-Una' => ['lat' => -1.4167, 'lng' => 121.5833],
            'Kabupaten Tolitoli' => ['lat' => 0.8667, 'lng' => 120.7833],
        
            // Sulawesi Selatan
            'Makassar' => ['lat' => -5.1477, 'lng' => 119.4327],
            'Palopo' => ['lat' => -2.9833, 'lng' => 120.2000],
            'Parepare' => ['lat' => -4.0167, 'lng' => 119.6167],
            'Kabupaten Bantaeng' => ['lat' => -5.5167, 'lng' => 120.0167],
            'Kabupaten Barru' => ['lat' => -4.4167, 'lng' => 119.6333],
            'Kabupaten Bone' => ['lat' => -4.7333, 'lng' => 120.2500],
            'Kabupaten Bulukumba' => ['lat' => -5.5333, 'lng' => 120.1833],
            'Kabupaten Enrekang' => ['lat' => -3.5500, 'lng' => 119.7833],
            'Kabupaten Gowa' => ['lat' => -5.3000, 'lng' => 119.7833],
            'Kabupaten Jeneponto' => ['lat' => -5.6333, 'lng' => 119.7167],
            'Kabupaten Luwu' => ['lat' => -2.9167, 'lng' => 120.2500],
            'Kabupaten Luwu Timur' => ['lat' => -2.5667, 'lng' => 120.8833],
            'Kabupaten Luwu Utara' => ['lat' => -2.6000, 'lng' => 120.1833],
            'Kabupaten Maros' => ['lat' => -4.9833, 'lng' => 119.5833],
            'Kabupaten Pangkajene Kepulauan' => ['lat' => -4.7667, 'lng' => 119.5500],
            'Kabupaten Pinrang' => ['lat' => -3.6333, 'lng' => 119.6167],
            'Kabupaten Selayar' => ['lat' => -6.1167, 'lng' => 120.4667],
            'Kabupaten Sidenreng Rappang' => ['lat' => -3.8167, 'lng' => 120.0667],
            'Kabupaten Sinjai' => ['lat' => -5.1500, 'lng' => 120.2500],
            'Kabupaten Soppeng' => ['lat' => -4.3500, 'lng' => 120.0833],
            'Kabupaten Takalar' => ['lat' => -5.4167, 'lng' => 119.4833],
            'Kabupaten Tana Toraja' => ['lat' => -3.0833, 'lng' => 119.8500],
            'Kabupaten Toraja Utara' => ['lat' => -2.9833, 'lng' => 119.8333],
            'Kabupaten Wajo' => ['lat' => -4.0000, 'lng' => 120.0333],
        
            // Sulawesi Tenggara
            'Kendari' => ['lat' => -3.9450, 'lng' => 122.5986],
            'Baubau' => ['lat' => -5.4667, 'lng' => 122.6000],
            'Kabupaten Bombana' => ['lat' => -4.6167, 'lng' => 121.8833],
            'Kabupaten Buton' => ['lat' => -5.2167, 'lng' => 122.9167],
            'Kabupaten Buton Selatan' => ['lat' => -5.6667, 'lng' => 122.8833],
            'Kabupaten Buton Tengah' => ['lat' => -5.0167, 'lng' => 122.7167],
            'Kabupaten Buton Utara' => ['lat' => -4.8000, 'lng' => 123.0333],
            'Kabupaten Kolaka' => ['lat' => -4.0500, 'lng' => 121.5833],
            'Kabupaten Kolaka Timur' => ['lat' => -3.8333, 'lng' => 121.9167],
            'Kabupaten Kolaka Utara' => ['lat' => -3.2833, 'lng' => 121.1667],
            'Kabupaten Konawe' => ['lat' => -3.9167, 'lng' => 122.2500],
            'Kabupaten Konawe Kepulauan' => ['lat' => -3.6167, 'lng' => 123.1333],
            'Kabupaten Konawe Selatan' => ['lat' => -4.1667, 'lng' => 122.4167],
            'Kabupaten Konawe Utara' => ['lat' => -3.7333, 'lng' => 121.9333],
            'Kabupaten Muna' => ['lat' => -4.8833, 'lng' => 122.6833],
            'Kabupaten Muna Barat' => ['lat' => -5.0167, 'lng' => 122.4500],
            'Kabupaten Wakatobi' => ['lat' => -5.2500, 'lng' => 123.6000],
        
            // Gorontalo
            'Gorontalo' => ['lat' => 0.5435, 'lng' => 123.0596],
            'Kabupaten Boalemo' => ['lat' => 0.7167, 'lng' => 122.2333],
            'Kabupaten Bone Bolango' => ['lat' => 0.5667, 'lng' => 123.0667],
            'Kabupaten Gorontalo' => ['lat' => 0.6167, 'lng' => 122.8333],
            'Kabupaten Gorontalo Utara' => ['lat' => 0.8167, 'lng' => 122.6833],
            'Kabupaten Pohuwato' => ['lat' => 0.7333, 'lng' => 121.6000],
        
            // Sulawesi Barat
            'Mamuju' => ['lat' => -2.6833, 'lng' => 119.4167],
            'Kabupaten Majene' => ['lat' => -3.5500, 'lng' => 118.9667],
            'Kabupaten Mamasa' => ['lat' => -2.9833, 'lng' => 119.3000],
            'Kabupaten Mamuju' => ['lat' => -2.5333, 'lng' => 119.4167],
            'Kabupaten Mamuju Tengah' => ['lat' => -2.1333, 'lng' => 119.3333],
            'Kabupaten Mamuju Utara' => ['lat' => -1.3667, 'lng' => 119.4167],
            'Kabupaten Polewali Mandar' => ['lat' => -3.4167, 'lng' => 119.3500],
        
            // Bali
            'Denpasar' => ['lat' => -8.6500, 'lng' => 115.2167],
            'Kabupaten Badung' => ['lat' => -8.5500, 'lng' => 115.1667],
            'Kabupaten Bangli' => ['lat' => -8.3000, 'lng' => 115.3500],
            'Kabupaten Buleleng' => ['lat' => -8.1167, 'lng' => 115.0833],
            'Kabupaten Gianyar' => ['lat' => -8.5333, 'lng' => 115.3333],
            'Kabupaten Jembrana' => ['lat' => -8.3500, 'lng' => 114.6167],
            'Kabupaten Karangasem' => ['lat' => -8.4500, 'lng' => 115.6167],
            'Kabupaten Klungkung' => ['lat' => -8.5333, 'lng' => 115.4000],
            'Kabupaten Tabanan' => ['lat' => -8.5333, 'lng' => 115.1167],
        
            // Nusa Tenggara Barat
            'Mataram' => ['lat' => -8.5833, 'lng' => 116.1167],
            'Bima' => ['lat' => -8.4667, 'lng' => 118.7167],
            'Kabupaten Bima' => ['lat' => -8.5500, 'lng' => 118.7333],
            'Kabupaten Dompu' => ['lat' => -8.5333, 'lng' => 118.4667],
            'Kabupaten Lombok Barat' => ['lat' => -8.6500, 'lng' => 116.1167],
            'Kabupaten Lombok Tengah' => ['lat' => -8.7000, 'lng' => 116.2667],
            'Kabupaten Lombok Timur' => ['lat' => -8.5833, 'lng' => 116.5333],
            'Kabupaten Lombok Utara' => ['lat' => -8.3333, 'lng' => 116.3333],
            'Kabupaten Sumbawa' => ['lat' => -8.8667, 'lng' => 117.4167],
            'Kabupaten Sumbawa Barat' => ['lat' => -8.7167, 'lng' => 116.8167],
        
            // Nusa Tenggara Timur
            'Kupang' => ['lat' => -10.1718, 'lng' => 123.6075],
            'Kabupaten Alor' => ['lat' => -8.2167, 'lng' => 124.5500],
            'Kabupaten Belu' => ['lat' => -9.4167, 'lng' => 124.9000],
            'Kabupaten Ende' => ['lat' => -8.8333, 'lng' => 121.6833],
            'Kabupaten Flores Timur' => ['lat' => -8.2167, 'lng' => 122.9500],
            'Kabupaten Kupang' => ['lat' => -9.8833, 'lng' => 123.8833],
            'Kabupaten Lembata' => ['lat' => -8.3833, 'lng' => 123.5167],
            'Kabupaten Malaka' => ['lat' => -9.5667, 'lng' => 124.9000],
            'Kabupaten Manggarai' => ['lat' => -8.6167, 'lng' => 120.4500],
            'Kabupaten Manggarai Barat' => ['lat' => -8.6833, 'lng' => 120.2000],
            'Kabupaten Manggarai Timur' => ['lat' => -8.5500, 'lng' => 120.6833],
            'Kabupaten Nagekeo' => ['lat' => -8.7500, 'lng' => 121.4167],
            'Kabupaten Ngada' => ['lat' => -8.6667, 'lng' => 120.9500],
            'Kabupaten Rote Ndao' => ['lat' => -10.7333, 'lng' => 123.1167],
            'Kabupaten Sabu Raijua' => ['lat' => -10.5167, 'lng' => 121.8833],
            'Kabupaten Sikka' => ['lat' => -8.6500, 'lng' => 122.2333],
            'Kabupaten Sumba Barat' => ['lat' => -9.6667, 'lng' => 119.4167],
            'Kabupaten Sumba Barat Daya' => ['lat' => -9.5167, 'lng' => 119.1667],
            'Kabupaten Sumba Tengah' => ['lat' => -9.4833, 'lng' => 119.7333],
            'Kabupaten Sumba Timur' => ['lat' => -9.8000, 'lng' => 120.2667],
            'Kabupaten Timor Tengah Selatan' => ['lat' => -9.8333, 'lng' => 124.0833],
            'Kabupaten Timor Tengah Utara' => ['lat' => -9.2500, 'lng' => 124.4833],
            'Kabupaten Timor Tenggara Selatan' => ['lat' => -10.1667, 'lng' => 124.3000],
        
            // Maluku
            'Ambon' => ['lat' => -3.6954, 'lng' => 128.1814],
            'Tual' => ['lat' => -5.6333, 'lng' => 132.7500],
            'Kabupaten Buru' => ['lat' => -3.3500, 'lng' => 126.6333],
            'Kabupaten Buru Selatan' => ['lat' => -3.6833, 'lng' => 126.5833],
            'Kabupaten Kepulauan Aru' => ['lat' => -6.1833, 'lng' => 134.5167],
            'Kabupaten Maluku Barat Daya' => ['lat' => -7.8333, 'lng' => 126.5667],
            'Kabupaten Maluku Tengah' => ['lat' => -3.1833, 'lng' => 128.9500],
            'Kabupaten Maluku Tenggara' => ['lat' => -5.8167, 'lng' => 132.7167],
            'Kabupaten Maluku Tenggara Barat' => ['lat' => -7.5333, 'lng' => 131.1167],
            'Kabupaten Seram Bagian Barat' => ['lat' => -3.0833, 'lng' => 128.1167],
            'Kabupaten Seram Bagian Timur' => ['lat' => -3.0333, 'lng' => 129.4833],
        
            // Maluku Utara
            'Ternate' => ['lat' => 0.7833, 'lng' => 127.3667],
            'Tidore Kepulauan' => ['lat' => 0.6833, 'lng' => 127.4000],
            'Kabupaten Halmahera Barat' => ['lat' => 1.4167, 'lng' => 127.5333],
            'Kabupaten Halmahera Tengah' => ['lat' => 0.6000, 'lng' => 128.0500],
            'Kabupaten Halmahera Timur' => ['lat' => 1.0167, 'lng' => 128.3667],
            'Kabupaten Halmahera Selatan' => ['lat' => -0.5000, 'lng' => 127.5833],
            'Kabupaten Halmahera Utara' => ['lat' => 1.8000, 'lng' => 127.8333],
            'Kabupaten Kepulauan Sula' => ['lat' => -1.6667, 'lng' => 125.3667],
            'Kabupaten Pulau Morotai' => ['lat' => 2.3167, 'lng' => 128.4000],
            'Kabupaten Pulau Taliabu' => ['lat' => -1.8333, 'lng' => 124.7667],
        
            // Papua Barat
            'Manokwari' => ['lat' => -0.8667, 'lng' => 134.0833],
            'Sorong' => ['lat' => -0.8667, 'lng' => 131.2500],
            'Kabupaten Fakfak' => ['lat' => -2.9167, 'lng' => 132.3000],
            'Kabupaten Kaimana' => ['lat' => -3.6500, 'lng' => 133.7000],
            'Kabupaten Manokwari' => ['lat' => -0.8667, 'lng' => 134.0833],
            'Kabupaten Manokwari Selatan' => ['lat' => -1.7667, 'lng' => 134.0667],
            'Kabupaten Maybrat' => ['lat' => -1.2500, 'lng' => 132.2500],
            'Kabupaten Pegunungan Arfak' => ['lat' => -1.3000, 'lng' => 133.7833],
            'Kabupaten Raja Ampat' => ['lat' => -0.2333, 'lng' => 130.8167],
            'Kabupaten Sorong' => ['lat' => -1.1167, 'lng' => 131.1000],
            'Kabupaten Sorong Selatan' => ['lat' => -1.8833, 'lng' => 132.0833],
            'Kabupaten Tambrauw' => ['lat' => -0.6667, 'lng' => 132.0833],
            'Kabupaten Teluk Bintuni' => ['lat' => -2.0833, 'lng' => 133.4167],
            'Kabupaten Teluk Wondama' => ['lat' => -2.7000, 'lng' => 134.4167],
        
            // Papua Barat Daya
            'Kabupaten Maybrat' => ['lat' => -1.2500, 'lng' => 132.2500],
            'Kabupaten Raja Ampat' => ['lat' => -0.2333, 'lng' => 130.8167],
            'Kabupaten Sorong' => ['lat' => -1.1167, 'lng' => 131.1000],
            'Kabupaten Sorong Selatan' => ['lat' => -1.8833, 'lng' => 132.0833],
            'Kabupaten Tambrauw' => ['lat' => -0.6667, 'lng' => 132.0833],
        
            // Papua Tengah
            'Kabupaten Nabire' => ['lat' => -3.3667, 'lng' => 135.4833],
            'Kabupaten Paniai' => ['lat' => -4.0000, 'lng' => 136.3500],
            'Kabupaten Puncak Jaya' => ['lat' => -4.0833, 'lng' => 137.1833],
            'Kabupaten Puncak' => ['lat' => -4.3333, 'lng' => 137.4833],
            'Kabupaten Dogiyai' => ['lat' => -4.1167, 'lng' => 135.7000],
            'Kabupaten Deiyai' => ['lat' => -4.2000, 'lng' => 136.2500],
            'Kabupaten Intan Jaya' => ['lat' => -3.9500, 'lng' => 136.7000],
            'Kabupaten Mimika' => ['lat' => -4.5333, 'lng' => 136.5500],
        
            // Papua Pegunungan
            'Kabupaten Jayawijaya' => ['lat' => -4.0833, 'lng' => 138.9000],
            'Kabupaten Lanny Jaya' => ['lat' => -3.9167, 'lng' => 138.3000],
            'Kabupaten Mamberamo Tengah' => ['lat' => -2.6500, 'lng' => 138.2000],
            'Kabupaten Nduga' => ['lat' => -4.4167, 'lng' => 138.2000],
            'Kabupaten Tolikara' => ['lat' => -3.4833, 'lng' => 138.5000],
            'Kabupaten Yahukimo' => ['lat' => -4.5167, 'lng' => 139.5167],
            'Kabupaten Yalimo' => ['lat' => -3.8333, 'lng' => 139.4167],
            'Kabupaten Pegunungan Bintang' => ['lat' => -4.9333, 'lng' => 140.4167],
        
            // Papua Selatan
            'Kabupaten Asmat' => ['lat' => -5.0500, 'lng' => 138.4333],
            'Kabupaten Boven Digoel' => ['lat' => -5.7333, 'lng' => 140.3667],
            'Kabupaten Mappi' => ['lat' => -6.4833, 'lng' => 139.7500],
            'Kabupaten Merauke' => ['lat' => -8.4833, 'lng' => 140.4000],
        
            // Papua
            'Jayapura' => ['lat' => -2.5489, 'lng' => 140.7197],
            'Kabupaten Biak Numfor' => ['lat' => -1.1667, 'lng' => 136.1000],
            'Kabupaten Jayapura' => ['lat' => -2.5333, 'lng' => 140.7000],
            'Kabupaten Keerom' => ['lat' => -3.2167, 'lng' => 140.5833],
            'Kabupaten Kepulauan Yapen' => ['lat' => -1.7500, 'lng' => 136.2167],
            'Kabupaten Mamberamo Raya' => ['lat' => -2.0000, 'lng' => 137.8833],
            'Kabupaten Sarmi' => ['lat' => -1.8833, 'lng' => 138.7833],
            'Kabupaten Supiori' => ['lat' => -0.7500, 'lng' => 135.5000],
            'Kabupaten Waropen' => ['lat' => -2.0833, 'lng' => 136.6167],
        
            // Kota dan Kabupaten tambahan yang mungkin terlewat
            'Cilegon' => ['lat' => -6.0167, 'lng' => 106.0333],
            'Serang' => ['lat' => -6.1167, 'lng' => 106.1500],
            'Pematangsiantar' => ['lat' => 2.9667, 'lng' => 99.0667],
            'Tebing Tinggi' => ['lat' => 3.3167, 'lng' => 99.1500],
            'Binjai' => ['lat' => 3.6000, 'lng' => 98.4833],
            'Padangsidimpuan' => ['lat' => 1.3833, 'lng' => 99.2667],
            'Gunungsitoli' => ['lat' => 1.2833, 'lng' => 97.6167],
            'Tanjungbalai' => ['lat' => 2.9667, 'lng' => 99.8000],
            'Sibolga' => ['lat' => 1.7333, 'lng' => 98.7833],
            'Bukittinggi' => ['lat' => -0.3000, 'lng' => 100.3667],
            'Padangpanjang' => ['lat' => -0.4667, 'lng' => 100.4000],
            'Payakumbuh' => ['lat' => -0.2167, 'lng' => 100.6333],
            'Pariaman' => ['lat' => -0.6167, 'lng' => 100.1167],
            'Sawahlunto' => ['lat' => -0.6833, 'lng' => 100.7833],
            'Solok' => ['lat' => -0.8000, 'lng' => 100.6500],
            'Dumai' => ['lat' => 1.6833, 'lng' => 101.4500],
            'Sungai Penuh' => ['lat' => -2.0667, 'lng' => 101.3833],
            'Lubuklinggau' => ['lat' => -3.3000, 'lng' => 102.8667],
            'Pagar Alam' => ['lat' => -4.0667, 'lng' => 103.2333],
            'Prabumulih' => ['lat' => -3.4333, 'lng' => 104.2333],
            'Metro' => ['lat' => -5.1133, 'lng' => 105.3067]
        ];


        // Konversi data untuk map (per kota)
        $kotaData = [];
        foreach ($kandidatPerKota as $kota) {
            $namaKota = $kota->kota_domisili;
            if (isset($koordinatKota[$namaKota])) {
                $kotaData[] = [
                    'nama' => $namaKota,
                    'kandidat' => $kota->total,
                    'lat' => $koordinatKota[$namaKota]['lat'],
                    'lng' => $koordinatKota[$namaKota]['lng']
                ];
            }
        }
        
        $lowonganList = CareerPosition::where('status', 'open')
            ->orderBy('position_title')
            ->get(['id', 'position_title']);

        return view('Gondowangi.Kandidat.Admin.dashboard', compact(
            'totalKaryawan',
            'menungguVerifikasi', 
            'terverifikasi',
            'baruHariIni',
            'lowonganAktif',
            'monthlyData',
            'aktivitasTerbaru',
            'kotaData',
            'lowonganList'
        ));
    }

    public function getKandidatData(Request $request)
    {
        $query = Karyawan::with(['posisilamaran', 'credentials']);

        // Filter berdasarkan nama atau email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan posisi
        if ($request->filled('position')) {
            $query->where('posisi_dilamar_id', $request->position);
        }

        // Filter berdasarkan lokasi
        if ($request->filled('location')) {
            $query->where('kota_domisili', $request->location);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan pengalaman kerja
        if ($request->filled('experience')) {
            $query->whereJsonContains('pengalaman_kerja', ['nama_perusahaan' => $request->experience]);
        }

        // Filter berdasarkan pendidikan
        if ($request->filled('education')) {
            $query->whereJsonContains('pendidikan_formal', ['nama_sekolah' => $request->education]);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $kandidat = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $kandidat->map(function($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'email' => $item->email,
                    'posisi' => $item->posisilamaran->position_title ?? 'N/A',
                    'lokasi' => $item->kota_domisili ?? 'N/A',
                    'tanggal_daftar' => $item->created_at->format('d/m/Y'),
                    'status' => $item->status,
                    'score' => $this->calculateScore($item), // Method untuk menghitung score
                    'foto' => $item->foto ? Storage::url($item->foto) : null,
                    'cv' => $item->cv ? Storage::url($item->cv) : null,
                    'no_telepon' => $item->no_telepon,
                    'tanggal_lahir' => $item->tanggal_lahir,
                    'gaji_terakhir' => $item->gaji_terakhir,
                    'gaji_diharapkan' => $item->gaji_diharapkan,
                    'pendidikan_formal' => $item->pendidikan_formal,
                    'pengalaman_kerja' => $item->pengalaman_kerja,
                    'informasi_tambahan' => $item->informasi_tambahan
                ];
            })
        ]);
    }

    // Method untuk mengambil data filter options
    public function getFilterOptions()
    {
        $positions = CareerPosition::select('id', 'position_title')
            ->where('status', 'open')
            ->orderBy('position_title')
            ->get();

        $locations = Karyawan::select('kota_domisili')
            ->whereNotNull('kota_domisili')
            ->where('kota_domisili', '!=', '')
            ->groupBy('kota_domisili')
            ->orderBy('kota_domisili')
            ->pluck('kota_domisili');

        // Ambil data pengalaman kerja unik
        $experiences = Karyawan::whereNotNull('pengalaman_kerja')
            ->get()
            ->pluck('pengalaman_kerja')
            ->flatten()
            ->pluck('nama_perusahaan')
            ->unique()
            ->filter()
            ->values();

        // Ambil data pendidikan unik
        $educations = Karyawan::whereNotNull('pendidikan_formal')
            ->get()
            ->pluck('pendidikan_formal')
            ->flatten()
            ->pluck('nama_sekolah')
            ->unique()
            ->filter()
            ->values();

        return response()->json([
            'positions' => $positions,
            'locations' => $locations,
            'experiences' => $experiences,
            'educations' => $educations
        ]);
    }

    // Method untuk menghitung score kandidat
    private function calculateScore($karyawan)
    {
        $score = 0;
        
        // Score berdasarkan kelengkapan data
        if ($karyawan->cv) $score += 20;
        if ($karyawan->foto) $score += 10;
        if ($karyawan->pendidikan_formal) $score += 25;
        if ($karyawan->pengalaman_kerja) $score += 30;
        if ($karyawan->informasi_tambahan) $score += 15;
        
        return $score;
    }

    // Method untuk update status kandidat
    public function updateKandidatStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,save'
        ]);

        $karyawan = Karyawan::findOrFail($id);
        $karyawan->status = $request->status;
        $karyawan->save();

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui'
        ]);
    }

    // Method baru untuk mendapatkan detail kota
    public function getCityDetails(Request $request)
    {
        $kotaName = $request->input('kota');
        
        if (!$kotaName) {
            return response()->json(['error' => 'Nama kota tidak ditemukan'], 400);
        }

        // Ambil detail kandidat per kota dengan status
        $detailKota = Karyawan::with(['posisilamaran'])
            ->where('kota_domisili', $kotaName)
            ->select('nama', 'email', 'no_telepon', 'status', 'posisi_dilamar_id', 'created_at', 'gaji_diharapkan')
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung statistik per status
        $statusStats = Karyawan::where('kota_domisili', $kotaName)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        // Ambil data posisi yang paling diminati di kota ini
        $posisiPopuler = Karyawan::with(['posisilamaran'])
            ->where('kota_domisili', $kotaName)
            ->whereNotNull('posisi_dilamar_id')
            ->select('posisi_dilamar_id', DB::raw('count(*) as total'))
            ->groupBy('posisi_dilamar_id')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        // Statistik gaji rata-rata
        $avgGaji = Karyawan::where('kota_domisili', $kotaName)
            ->whereNotNull('gaji_diharapkan')
            ->avg('gaji_diharapkan');

        return response()->json([
            'kota' => $kotaName,
            'total_kandidat' => $detailKota->count(),
            'kandidat_list' => $detailKota,
            'status_stats' => $statusStats,
            'posisi_populer' => $posisiPopuler,
            'avg_gaji' => $avgGaji ? number_format($avgGaji, 0, ',', '.') : 0
        ]);
    }

    // Method untuk mengambil data chart aplikasi per lowongan
    public function getApplicationsByPosition(Request $request)
    {
        $positionId = $request->input('position_id');
        
        if (!$positionId) {
            return response()->json(['error' => 'Position ID required'], 400);
        }

        // Ambil data aplikasi per bulan untuk posisi tertentu (6 bulan terakhir)
        $applicationData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Karyawan::where('posisi_dilamar_id', $positionId)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $applicationData[] = [
                'month' => $date->format('M Y'),
                'count' => $count
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $applicationData
        ]);
    }

    // Method untuk mengambil data distribusi status untuk posisi tertentu
    public function getStatusDistribution(Request $request)
    {
        $positionId = $request->input('position_id');
        
        $query = Karyawan::select('status', DB::raw('count(*) as total'))
            ->groupBy('status');
        
        if ($positionId) {
            $query->where('posisi_dilamar_id', $positionId);
        }
        
        $statusData = $query->get();
        
        // Mapping status ke label yang lebih user-friendly
        $statusLabels = [
            'pending' => 'Menunggu Verifikasi',
            'lanjut' => 'Lanjut ke Tahap Berikutnya',
            'ditolak' => 'Ditolak',
            'diterima' => 'Diterima',
            'simpan' => 'Disimpan'
        ];

        $chartData = [];
        foreach ($statusData as $status) {
            $chartData[] = [
                'label' => $statusLabels[$status->status] ?? ucfirst($status->status),
                'value' => $status->total,
                'status' => $status->status
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $chartData
        ]);
    }

    // Method untuk mengambil data chart keseluruhan (jika tidak ada posisi dipilih)
    public function getAllApplicationsChart()
    {
        // Data aplikasi per bulan untuk semua posisi (6 bulan terakhir)
        $applicationData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Karyawan::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $applicationData[] = [
                'month' => $date->format('M Y'),
                'count' => $count
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $applicationData
        ]);
    }

    // Method untuk mengambil data distribusi status keseluruhan
    public function getAllStatusDistribution()
    {
        $statusData = Karyawan::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
        
        // Mapping status ke label yang lebih user-friendly
        $statusLabels = [
            'pending' => 'Menunggu Verifikasi',
            'lanjut' => 'Lanjut ke Tahap Berikutnya',
            'ditolak' => 'Ditolak',
            'diterima' => 'Diterima',
            'simpan' => 'Disimpan'
        ];

        $chartData = [];
        foreach ($statusData as $status) {
            $chartData[] = [
                'label' => $statusLabels[$status->status] ?? ucfirst($status->status),
                'value' => $status->total,
                'status' => $status->status
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $chartData
        ]);
    }
    
}
