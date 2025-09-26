<?php

namespace App\Http\Controllers\Gondowangi\Karir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CareerPosition;

class KarirController extends Controller
{
    public function index()
    {
        $careerPositions = CareerPosition::where('status', 'open')
           ->where('deadline', '>=', now()) // Hanya tampilkan yang belum expired
           ->orderBy('posted_date', 'desc')
           ->get();
    
        return view('Gondowangi.Karir.index', [
            'title' => 'Karir',
            'careerPositions' => $careerPositions
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
