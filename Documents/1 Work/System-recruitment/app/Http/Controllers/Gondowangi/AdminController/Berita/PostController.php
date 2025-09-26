<?php

namespace App\Http\Controllers\Gondowangi\AdminController\Berita;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quote;

class PostController extends Controller
{
    public function index()
    {
        $quote = Quote::getActiveQuote();
        
        return view('Gondowangi.Admin.Berita.Post', [
            'title' => 'Semua Postingan',
            'quote' => $quote
        ]);
    }

    public function updateQuote(Request $request)
    {
        $request->validate([
            'quote_text' => 'required|string|max:1000',
            'author' => 'required|string|max:255',
        ]);

        $quote = Quote::getActiveQuote();
        
        if ($quote) {
            $quote->update([
                'quote_text' => $request->quote_text,
                'author' => $request->author,
            ]);
        } else {
            Quote::create([
                'quote_text' => $request->quote_text,
                'author' => $request->author,
                'is_active' => true
            ]);
        }

        return redirect()->back()->with('success', 'Quote berhasil diperbarui!');
    }
}
