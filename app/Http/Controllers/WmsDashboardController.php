<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Models
use App\Models\IncomingGood;
use App\Models\StockMovement;
use App\Models\ReservationRequest;
use App\Models\CycleCount;
use App\Models\InventoryStock;
use App\Models\Material;

class WmsDashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('WmsDashboard');
    }

    public function getData(Request $request)
    {
        $startDate = $request->input('date_start') ? Carbon::parse($request->input('date_start'))->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->input('date_end') ? Carbon::parse($request->input('date_end'))->endOfDay() : Carbon::now()->endOfDay();

        // 1. Total Incoming (Receiving)
        $totalIncoming = IncomingGood::whereBetween('created_at', [$startDate, $endDate])->count();
        
        // 2. Total Outgoing (Picking List - Completed)
        // We count ReservationRequests that are completed within the period
        $totalOutgoing = ReservationRequest::whereIn('status', ['Completed', 'Short-Pick'])
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        // 3. Incoming by Category (Revised: Source from Material table)
        // Count of Items Received grouped by their Material Category
        $incomingByCategory = DB::table('incoming_goods_items as item')
            ->join('incoming_goods as header', 'item.incoming_id', '=', 'header.id')
            ->join('materials as m', 'item.material_id', '=', 'm.id')
            ->whereBetween('header.created_at', [$startDate, $endDate])
            ->select('m.kategori', DB::raw('count(*) as total'))
            ->groupBy('m.kategori')
            ->orderByDesc('total')
            ->get();

        // 3b. Incoming by Type (RM vs PM) - from incoming_goods table
        $incomingByType = DB::table('incoming_goods')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('kategori', DB::raw('count(*) as total'))
            ->groupBy('kategori')
            ->get();

        // 4. Lead Time (Picking Duration)
        // Avg duration from Request Created to Request Completed/Short-Pick
        // We can try to differentiate RM and PM based on the items in the request, 
        // but for now let's get a general average and try to split if possible.
        // To split RM/PM, we'd need to join tables. Let's do a simplified version first.
        
        $completedRequests = ReservationRequest::whereIn('status', ['Completed', 'Short-Pick'])
            ->whereNotNull('picking_started_at')
            ->whereNotNull('picking_completed_at')
            ->whereBetween('picking_completed_at', [$startDate, $endDate])
            ->get();

        $leadTimeData = $completedRequests->map(function($req) {
            $started = Carbon::parse($req->picking_started_at);
            $completed = Carbon::parse($req->picking_completed_at);
            $durationMinutes = $completed->diffInMinutes($started);
            
            return [
                'duration' => $durationMinutes,
                'type' => 'General' 
            ];
        });

        $avgLeadTime = $leadTimeData->avg('duration');
        
        // Separate RM/PM if possible based on Material Category/Type
        // Let's rely on a more complex query if needed, or iterate.
        // Assuming we want to show single number for now or split if data available.
        // Let's refine the query to join materials.
        
        $leadTimeByType = DB::table('reservation_requests as rr')
            ->join('reservation_request_items as rri', 'rr.id', '=', 'rri.reservation_request_id')
            ->join('materials as m', function($join) {
                // Determine material link. checking rri structure. 
                // Usually rri might have material_id or kode_item key.
                // Let's check ReservationRequestItem model...
                // It usually has 'kode_item' strings matching 'materials.kode_item' or similar?
                // Or maybe 'reservations' table is better source?
                // Let's use 'reservations' table which definitely links to materials.
             }) 
             // Logic complexity: a request can have mixed items. 
             // Let's stick to overall average for now, and maybe a simple split by 'kategori' if Materials have it.
        ;

        // Better approach for Lead Time:
        // Calculate average duration in minutes
        $avgLeadTimeVal = $leadTimeData->count() > 0 ? round($leadTimeData->avg('duration'), 0) : 0;
        // Format to hours/min
        $leadTimeFormatted = $this->formatDuration($avgLeadTimeVal);
        

        // 5. Stock Accuracy (Cycle Count)
        // From CycleCount table, status APPROVED.
        // Accuracy = (physical / system) * 100
        // We average this %.
        $cycleCounts = CycleCount::where('status', 'APPROVED')
            ->whereBetween('count_date', [$startDate, $endDate])
            ->get();
            
        $avgAccuracy = 100;
        if ($cycleCounts->count() > 0) {
            $totalAcc = 0;
            foreach($cycleCounts as $cc) {
                $sys = (float)$cc->system_qty;
                $phys = (float)$cc->physical_qty;
                if ($sys > 0) {
                    $acc = ($phys / $sys) * 100;
                    // Cap at 100? No, accuracy can be > 100 if surplus? 
                    // Usually accuracy is ABS(diff)/sys? Or just direct ratio?
                    // User asked for "% accuracy".
                    // Let's use direct ratio. 
                    // If phys 10, sys 10 -> 100%
                    // If phys 9, sys 10 -> 90%
                    // If phys 11, sys 10 -> 110% (or maybe variance is -10%?)
                    // Let's stick to direct match % for now.
                    $totalAcc += $acc;
                } else {
                    // System 0, Phys 0 -> 100%
                    // System 0, Phys 5 -> 0% accuracy?
                    if ($phys == 0) $totalAcc += 100;
                    else $totalAcc += 0;
                }
            }
            $avgAccuracy = round($totalAcc / $cycleCounts->count(), 2);
        }

        // 6. SKU On Hand (Total Sku that has Stock > 0)
        // This is a snapshot, not really time-bound, but usually "Current On Hand".
        // Use standard InventoryStock query.
        // 6. SKU On Hand (Total Sku that has Stock > 0)
        $skuOnHand = InventoryStock::where('qty_on_hand', '>', 0)
            ->distinct('material_id')
            ->count('material_id');

        // --- TREND CHARTS DATA ---
        
        // A. Incoming Trend (Daily)
        $incomingTrend = IncomingGood::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as value'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // B. Outgoing Trend (Daily)
        $outgoingTrend = ReservationRequest::whereIn('status', ['Completed', 'Short-Pick'])
            ->whereBetween('updated_at', [$startDate, $endDate]) // Use updated_at or picking_completed_at? picking_completed_at IS safer.
            ->whereNotNull('picking_completed_at')
            ->select(DB::raw('DATE(picking_completed_at) as date'), DB::raw('count(*) as value'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // C. Lead Time Trend (Daily Avg)
        // using raw sql for diff
        $leadTimeTrend = ReservationRequest::whereIn('status', ['Completed', 'Short-Pick'])
            ->whereNotNull('picking_started_at')
            ->whereNotNull('picking_completed_at')
            ->whereBetween('picking_completed_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(picking_completed_at) as date'), 
                DB::raw('AVG(TIMESTAMPDIFF(MINUTE, picking_started_at, picking_completed_at)) as value')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // D. Accuracy Trend (Daily Avg)
        // This is complex because accuracy is calc per item. Average of (phys/sys).
        // Let's do it via collection from the query we already have ($cycleCounts) to reuse logic.
        $accuracyTrend = $cycleCounts->groupBy(function($item) {
            return Carbon::parse($item->count_date)->format('Y-m-d');
        })->map(function($dayGroup) {
            $totalAcc = 0;
            foreach($dayGroup as $cc) {
                $sys = (float)$cc->system_qty;
                $phys = (float)$cc->physical_qty;
                if ($sys > 0) {
                     $totalAcc += ($phys / $sys) * 100;
                } else {
                     $totalAcc += ($phys == 0 ? 100 : 0);
                }
            }
            return round($totalAcc / $dayGroup->count(), 2);
        })->map(function($value, $date) {
            return ['date' => $date, 'value' => $value];
        })->values();

        // E. SKU On Hand Breakdown (Top 5 Categories) - For Chart
        $skuByCategory = DB::table('inventory_stock as s')
            ->join('materials as m', 's.material_id', '=', 'm.id')
            ->where('s.qty_on_hand', '>', 0)
            ->select('m.kategori', DB::raw('count(DISTINCT s.material_id) as value'))
            ->groupBy('m.kategori')
            ->orderByDesc('value')
            ->limit(5)
            ->get();


        return response()->json([
            'totalIncoming' => $totalIncoming,
            'totalOutgoing' => $totalOutgoing,
            'incomingByCategory' => $incomingByCategory,
            'incomingByType' => $incomingByType,
            'leadTime' => $leadTimeFormatted,
            'leadTimeRaw' => $avgLeadTimeVal, // minutes
            'stockAccuracy' => $avgAccuracy,
            'skuOnHand' => $skuOnHand,
            'trends' => [
                'incoming' => $incomingTrend,
                'outgoing' => $outgoingTrend,
                'leadTime' => $leadTimeTrend,
                'accuracy' => $accuracyTrend,
                'skuCategory' => $skuByCategory
            ],
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ]
        ]);
    }

    private function formatDuration($minutes)
    {
        if ($minutes < 60) {
            return "{$minutes} Mins";
        }
        $hours = floor($minutes / 60);
        $remMin = $minutes % 60;
        return "{$hours} Hours {$remMin} Mins";
    }
}
