<?php

namespace App\Http\Controllers;

use App\Models\CoinTransaction;
use Illuminate\Http\Request;

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
