<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CoinTransaction;

class UserWalletController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $transactions = CoinTransaction::where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('pages.user-panel.wallet', compact('user', 'transactions'));
    }
}
