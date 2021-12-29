<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocalOrder;
use Auth;

class HomeController extends Controller
{
    public function index()
    {

        if(Auth::user()->role_id == 1){
            $local_order = LocalOrder::with(['sales_specialist', 'customer', 'address', 'items.product'])->where('confirmation_status', 'ERR')->get();
            return view('dashboard.index', compact('local_order'));
        }
    	return view('dashboard.index');
    }
}
