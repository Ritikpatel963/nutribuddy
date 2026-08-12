<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class CryptocurrencyController extends Controller
{
    public function marketplace()
    {
        return view('cryptocurrency/marketplace');
    }

    public function marketplaceDetails()
    {
        return view('cryptocurrency/marketplaceDetails');
    }
    
    public function portfolio()
    {
        return view('cryptocurrency/portfolio');
    }
    
    public function wallet()
    {
        return view('cryptocurrency/wallet');
    }

}
