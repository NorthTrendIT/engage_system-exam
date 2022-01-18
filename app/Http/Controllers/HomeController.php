<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocalOrder;
use App\Models\CustomerPromotion;
use Auth;

class HomeController extends Controller
{
    public function index()
    {

        if(Auth::user()->role_id == 1){
            $local_order = LocalOrder::where('confirmation_status', 'ERR')->get();
            $promotion =  CustomerPromotion::where(['is_sap_pushed' => 0, 'status' => 'approved'])->get();
            return view('dashboard.index', compact('local_order', 'promotion'));
        }

        if(Auth::user()->role_id != 1){
            $notification = getMyNotifications();
            // dd($notification);
            return view('dashboard.index', compact('notification'));
        }
    	return view('dashboard.index');
    }
}
