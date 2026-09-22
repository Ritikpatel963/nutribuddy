<?php

namespace App\Http\Controllers\Admin\Nutrition;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DietRuleController extends Controller
{
    public function index()
    {
        return view('admin.nutrition.rules.index');
    }
}
