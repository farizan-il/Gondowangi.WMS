<?php

namespace App\Http\Controllers\Gondowangi\AdminController\TentangKami;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index()
    {
        return view('Gondowangi.Admin.TentangKami.Produk', [
            'title' => 'Produk',
        ]);
    }
}
