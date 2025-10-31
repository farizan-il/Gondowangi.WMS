<?php

namespace App\Http\Controllers;

use App\Models\InventoryStock;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $inventoryItems = InventoryStock::with(['material', 'bin'])->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => $item->material->kategori,
                'kode' => $item->material->kode_item,
                'nama' => $item->material->nama_material,
                'lot' => $item->batch_lot,
                'lokasi' => $item->bin ? $item->bin->bin_code : 'N/A',
                'qty' => $item->qty_on_hand,
                'uom' => $item->uom,
                'expiredDate' => $item->exp_date->toDateString(),
                'status' => $item->status,
                'history' => [], // You might want to fetch this from StockMovement model later
            ];
        });

        $expiringSoonCount = InventoryStock::where('exp_date', '<=', now()->addDays(30))->where('exp_date', '>', now())->count();
        $expiredCount = InventoryStock::where('exp_date', '<=', now())->count();

        $alerts = [];
        if ($expiringSoonCount > 0) {
            $alerts[] = ['id' => '1', 'type' => 'warning', 'message' => "$expiringSoonCount item akan expired dalam 30 hari"];
        }
        if ($expiredCount > 0) {
            $alerts[] = ['id' => '2', 'type' => 'error', 'message' => "$expiredCount item sudah expired!"];
        }

        return Inertia::render('Dashboard', [
            'materialItems' => $inventoryItems,
            'alerts' => $alerts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
