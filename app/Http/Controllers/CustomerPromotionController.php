<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Promotions;
use App\Models\PromotionTypes;
use Validator;
use DataTables;
use Auth;

class CustomerPromotionController extends Controller
{
    public function index()
    {
    	dd("yes");
    }
}
