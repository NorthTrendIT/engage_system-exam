<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SapConnection;
use App\Models\Territory;
use App\Models\Customer;
use App\Models\User;
use App\Models\ProductGroup;
use App\Models\CustomerGroup;
use App\Models\Promotions;
use App\Models\Product;

class CommonController extends Controller
{
    // Get Business Unit
    public function getBusinessUnits(Request $request){
        $search = $request->search;

        $data = SapConnection::select('id','company_name');

        if($search != ''){
            $data = $data->where('company_name', 'like', '%' .$search . '%');
        }

        $data = $data->orderby('company_name','asc')->limit(50)->get();

        $response = array();
        foreach($data as $value){
            $response[] = array(
                "id"=>$value->id,
                "text"=>$value->company_name
            );
        }

        return response()->json($response);
    }

    // Get Territories
    public function getTerritory(Request $request){
        $response = array();
        $search = $request->search;

        $data = Territory::where('territory_id','!=','-2')->where('is_active', true)->orderby('description','asc')->select('id','description');

        if($search != ''){
            $data->where('description', 'like', '%' .$search . '%');
        }

        $data = $data->limit(50)->get();

        foreach($data as $value){
            $response[] = array(
                "id" => $value->id,
                "text" => $value->description
            );
        }

        return response()->json($response);
    }

    // Get Market Sector
    public function getMarketSector(Request $request){
        // dd($request->all());
        $response = array();
        if($request->sap_connection_id){
            $search = $request->search;

            $data = Customer::where('is_active', true)->orderby('u_sector','asc')->select('u_sector');

            if($search != ''){
                $data->where('u_sector', 'like', '%' .$search . '%');
            }

            if(@$request->sap_connection_id != ''){
                $data->where('sap_connection_id',@$request->sap_connection_id);
            }

            $data = $data->limit(50)->groupBy('u_sector')->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->u_sector,
                    "text" => @$value->u_sector_sap_value->value ?? $value->u_sector
                );
            }
        }

        return response()->json($response);
    }

    // Get Market Subsector
    public function getMarketSubSector(Request $request){
        $response = array();
        if($request->sap_connection_id){
            $search = $request->search;

            $data = Customer::where('is_active', true)->orderby('u_subsector','asc')->select('u_subsector');

            if($search != ''){
                $data->where('u_subsector', 'like', '%' .$search . '%');
            }

            if(@$request->sap_connection_id != ''){
                $data->where('sap_connection_id',@$request->sap_connection_id);
            }

            $data = $data->limit(50)->groupBy('u_subsector')->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->u_subsector,
                    "text" => @$value->u_subsector_sap_value->value ?? $value->u_subsector
                );
            }
        }

        return response()->json($response);
    }

    // Get Region
    public function getRegion(Request $request){
        $response = array();
        if($request->sap_connection_id){
            $search = $request->search;

            $data = Customer::where('is_active', true)->orderby('u_rgn','asc')->select('u_rgn')->limit(50)->groupBy('u_rgn');

            if($search != ''){
                $data->where('u_rgn', 'like', '%' .$search . '%');
            }

            if(@$request->sap_connection_id != ''){
                $data->where('sap_connection_id',@$request->sap_connection_id);
            }

            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->u_rgn,
                    "text" => $value->u_rgn
                );
            }
        }

        return response()->json($response);
    }

    // Get Province
    public function getProvince(Request $request){
        $response = array();
        if($request->sap_connection_id){
            $search = $request->search;

            $data = Customer::where('is_active', true)->orderby('u_province','asc')->select('u_province')->limit(50)->groupBy('u_province');

            if($search != ''){
                $data->where('u_province', 'like', '%' .$search . '%');
            }

            if(@$request->sap_connection_id != ''){
                $data->where('sap_connection_id',@$request->sap_connection_id);
            }

            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->u_province,
                    "text" => @$value->u_province_sap_value->value ?? $value->u_province,
                );
            }
        }

        return response()->json($response);
    }

    // Get City
    public function getCity(Request $request){
        $response = array();
        if($request->sap_connection_id){
            $search = $request->search;

            $data = Customer::where('is_active', true)->orderby('city','asc')->select('city')->limit(50)->groupBy('city');

            if($search != ''){
                $data->where('city', 'like', '%' .$search . '%');
            }

            if(@$request->sap_connection_id != ''){
                $data->where('sap_connection_id',@$request->sap_connection_id);
            }

            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->city,
                    "text" => $value->city,
                );
            }
        }

        return response()->json($response);
    }

    // Get Branch
    public function getBranch(Request $request){
        $response = array();
        if($request->sap_connection_id){
            $search = $request->search;

            // $data = Customer::where('is_active', true)->orderby('group_code','asc')->select('group_code')->limit(50)->groupBy('group_code');
            $data = CustomerGroup::orderby('name','asc')->limit(50);

            if($search != ''){
                $data->where('name', 'like', '%' .$search . '%');
            }

            if(@$request->sap_connection_id != ''){
                $data->where('sap_connection_id',@$request->sap_connection_id);
            }

            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->code,
                    "text" => $value->name,
                );
            }
        }

        return response()->json($response);
    }

    // Get Sales Specialist
    public function getSalesSpecialist(Request $request){
        $response = array();
        if($request->sap_connection_id){
            $search = $request->search;

            $data = User::where('is_active', true)->where('role_id', 2)->select('id', 'sales_specialist_name')->limit(50);

            if($search != ''){
                $data->where('sales_specialist_name', 'like', '%' .$search . '%');
            }

            $sap_connection_id = $request->sap_connection_id;
            if($request->sap_connection_id == 5){ //Solid Trend
                $sap_connection_id = 1;
            }

            if(@$request->sap_connection_id != ''){
                $data->where('sap_connection_id',@$sap_connection_id);
            }

            $data = $data->get();
            // dd($data);

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->id,
                    "text" => $value->sales_specialist_name,
                );
            }
        }

        return response()->json($response);
    }

    // Get Class
    public function getCustomerClass(Request $request){
        $response = array();
        if($request->sap_connection_id){
            $search = $request->search;

            $data = Customer::where('is_active', true)->orderby('u_classification','asc')->limit(50)->groupBy('u_classification');

            if($search != ''){
                //$data->where('u_classification', 'like', '%' .$search . '%');

                $data->whereHas('u_classification_sap_value', function($q) use ($search) {
                    $q->where('value','LIKE',"%".$search."%");
                });
            }

            if(@$request->sap_connection_id != ''){
                $data->where('sap_connection_id',@$request->sap_connection_id);
            }

            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->u_classification,
                    "text" => $value->u_classification_sap_value->value ?? $value->u_classification,
                );
            }
        }

        return response()->json($response);
    }

    // Get Brand
    public function getBrands(Request $request){

        $response = array();
        if($request->sap_connection_id){
            $search = $request->search;

            $data = ProductGroup::orderby('group_name','asc')
                                ->select('number','group_name')
                                ->where('is_active', true)
                                ->limit(50);

            if($search != ''){
                $data->where('group_name', 'like', '%' .$search . '%');
            }

            $sap_connection_id = $request->sap_connection_id;
            if($request->sap_connection_id == 5){ //Solid Trend
                $sap_connection_id = 1;
            }

            if(@$request->sap_connection_id != ''){
                $data->where('sap_connection_id',@$sap_connection_id);
            }

            $data->whereNotIn('group_name', ['Items', 'MKTG. MATERIALS', 'OFFICIAL DOCUMENT']);
            
            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->number,
                    "text" => $value->group_name
                );
            }
        }

        return response()->json($response);
    }

    // Get Users
    public function getUsers(Request $request){
        $search = $request->search;

        $data = User::where('department_id', '!=', 1)->select('id','sales_specialist_name');

        if($search != ''){
            $data = $data->where('sales_specialist_name', 'like', '%' .$search . '%');
        }

        if(@$request->sap_connection_id != ''){
            $data->where('sap_connection_id',@$request->sap_connection_id);
        }

        $data = $data->orderby('sales_specialist_name','asc')->limit(50)->get();

        $response = array();
        foreach($data as $value){
            $response[] = array(
                "id"=>$value->id,
                "text"=>$value->sales_specialist_name
            );
        }

        return response()->json($response);
    }

    // Get Promotion Code
    public function getPromotionCodes(Request $request){
        $search = $request->search;

        $data = Promotions::select('id','code');

        if($search != ''){
            $data = $data->where('code', 'like', '%' .$search . '%');
        }

        $data = $data->orderby('code','asc')->limit(50)->get();

        $response = array();
        foreach($data as $value){
            $response[] = array(
                "id"=>$value->id,
                "text"=>$value->code
            );
        }

        return response()->json($response);
    }

    // Get Customers
    public function getCustomer(Request $request){
        $response = array();
        if($request->sap_connection_id){
            $search = $request->search;

            $data = Customer::where('is_active', true)->orderby('card_name','asc')->select('id', 'card_name')->limit(50);

            if($search != ''){
                $data->where('card_name', 'like', '%' .$search . '%');
            }

            $sap_connection_id = $request->sap_connection_id;
            if($request->sap_connection_id == 5){
                //Solid Trend
                $sap_connection_id = 1;
            }

            if(@$request->sap_connection_id != ''){
                $data->where('sap_connection_id',@$sap_connection_id);
            }

            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->id,
                    "text" => $value->card_name,
                );
            }
        }

        return response()->json($response);
    }

    // Get Product Category
    public function getProductCategory(Request $request){
        $response = array();
        if($request->sap_connection_id != "" && @$request->items_group_code != ""){
            $data = Product::where('is_active', true)->select('items_group_code', 'u_tires')->where('items_group_code', $request->items_group_code)->where('sap_connection_id', $request->sap_connection_id)->whereNotNull('u_tires')->orderby('u_tires')->limit(50);

            if(@$request->search  != ''){
                $data->where('u_tires', 'like', '%' .$request->search . '%');
            }

            $data->whereHas('group', function($q){
                $q->where('is_active', true);
            });

            if(@$request->sap_connection_id != ''){
                $sap_connection_id = $request->sap_connection_id;

                if($request->sap_connection_id == 5){
                    //Solid Trend
                    $sap_connection_id = 1;
                }

                $data->where('sap_connection_id',@$sap_connection_id);
            }

            $data = $data->groupBy('u_tires')->get();
            // dd($data);
            foreach($data as $value){
                // dd($value);
                $response[] = array(
                    "id" => @$value->u_tires,
                    "text" => @$value->u_tires,
                );
            }
        }

        return response()->json($response);
    }

    // Get Product Line
    public function getProductLine(Request $request){
        $response = array();

        if(@$request->sap_connection_id != "" && @$request->items_group_code != ""){
            $data = Product::where('is_active', true)->where('items_group_code', $request->items_group_code)->where('sap_connection_id', $request->sap_connection_id)->whereNotNull('u_item_line')->orderby('u_item_line')->limit(50);

            if(@$request->search  != ''){
                $data->where('u_item_line', 'like', '%' .$request->search . '%');
            }

            $data->whereHas('group', function($q){
                $q->where('is_active', true);
            });

            $sap_connection_id = $request->sap_connection_id;
            if($request->sap_connection_id == 5){ //Solid Trend
                $sap_connection_id = 1;
            }

            if(@$request->sap_connection_id != ''){
                $data->where('sap_connection_id',@$sap_connection_id);
            }

            $data = $data->groupBy('u_item_line')->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => @$value->u_item_line,
                    "text" => @$value->u_item_line_sap_value->value ?? @$value->u_item_line,
                );
            }
        }

        return response()->json($response);
    }

    // Product Class
    public function getProductClass(Request $request){
        $response = array();

        if(@$request->sap_connection_id != "" && @$request->items_group_code != ""){
            $data = Product::where('is_active', true)->where('items_group_code', $request->items_group_code)->where('sap_connection_id', $request->sap_connection_id)->whereNotNull('item_class')->orderby('item_class')->limit(50);

            if(@$request->search  != ''){
                $data->where('item_class', 'like', '%' .$request->search . '%');
            }

            $data->whereHas('group', function($q){
                $q->where('is_active', true);
            });

            $sap_connection_id = $request->sap_connection_id;
            if($request->sap_connection_id == 5){ //Solid Trend
                $sap_connection_id = 1;
            }

            if(@$request->sap_connection_id != ''){
                $data->where('sap_connection_id',@$sap_connection_id);
            }

            $data = $data->groupBy('item_class')->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => @$value->item_class,
                    "text" => @$value->item_class,
                );
            }
        }

        return response()->json($response);
    }

    // Product Type
    public function getProductType(Request $request){
        $response = array();

        if(@$request->sap_connection_id != "" && @$request->items_group_code != ""){
            $data = Product::where('is_active', true)->where('items_group_code', $request->items_group_code)->where('sap_connection_id', $request->sap_connection_id)->whereNotNull('u_item_type')->orderby('u_item_type')->limit(50);

            if(@$request->search  != ''){
                $data->where('u_item_type', 'like', '%' .$request->search . '%');
            }

            $data->whereHas('group', function($q){
                $q->where('is_active', true);
            });

            $sap_connection_id = $request->sap_connection_id;
            if($request->sap_connection_id == 5){ //Solid Trend
                $sap_connection_id = 1;
            }

            if(@$request->sap_connection_id != ''){
                $data->where('sap_connection_id',@$sap_connection_id);
            }

            $data = $data->groupBy('u_item_type')->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => @$value->u_item_type,
                    "text" => @$value->u_item_type_sap_value->value ?? @$value->u_item_type,
                );
            }
        }

        return response()->json($response);
    }

    // Product Application
    public function getProductApplication(Request $request){
        $response = array();

        if(@$request->sap_connection_id != "" && @$request->items_group_code != ""){
            $data = Product::where('is_active', true)->where('items_group_code', $request->items_group_code)->where('sap_connection_id', $request->sap_connection_id)->whereNotNull('u_item_application')->orderby('u_item_application')->limit(50);

            if(@$request->search  != ''){
                $data->where('u_item_application', 'like', '%' .$request->search . '%');
            }

            $data->whereHas('group', function($q){
                $q->where('is_active', true);
            });

            $sap_connection_id = $request->sap_connection_id;
            if($request->sap_connection_id == 5){ //Solid Trend
                $sap_connection_id = 1;
            }

            if(@$request->sap_connection_id != ''){
                $data->where('sap_connection_id',@$sap_connection_id);
            }

            $data = $data->groupBy('u_item_application')->get();
            $response[] = array('id' => '', 'text' => 'All');
            foreach($data as $value){
                $response[] = array(
                    "id" => @$value->u_item_application,
                    "text" => @$value->u_item_application_sap_value->value ?? @$value->u_item_application,
                );
            }
        }

        return response()->json($response);
    }

    // Product Pattern
    public function getProductPattern(Request $request){
        $response = array();

        if(@$request->sap_connection_id != "" && @$request->items_group_code != ""){
            $data = Product::where('is_active', true)->where('items_group_code', $request->items_group_code)->where('sap_connection_id', $request->sap_connection_id)->whereNotNull('u_pattern2')->orderby('u_pattern2')->limit(50);

            if(@$request->search  != ''){
                $data->where('u_pattern2', 'like', '%' .$request->search . '%');
            }

            $data->whereHas('group', function($q){
                $q->where('is_active', true);
            });
            
            $sap_connection_id = $request->sap_connection_id;
            if($request->sap_connection_id == 5){ //Solid Trend
                $sap_connection_id = 1;
            }

            if(@$request->sap_connection_id != ''){
                $data->where('sap_connection_id',@$sap_connection_id);
            }

            $data = $data->groupBy('u_pattern2')->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => @$value->u_pattern2,
                    "text" => @$value->u_pattern2,
                );
            }
        }

        return response()->json($response);
    }
}
