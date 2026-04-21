<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class TableController extends Controller
{
    public function tableBasic()
    {
        return view('table/tableBasic');
    }
    
    public function tableData()
    {
        return view('table/tableData');
    }
    
}
