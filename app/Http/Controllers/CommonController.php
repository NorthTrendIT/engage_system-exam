<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SapConnection;
use App\Models\Territory;
use App\Models\Customer;
use App\Models\User;
use App\Models\ProductGroup;
use App\Models\CustomerGroup;

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

            $data = Customer::where('is_active', true)->orderby('u_msec','asc')->select('u_msec');

            if($search != ''){
                $data->where('u_msec', 'like', '%' .$search . '%');
            }

            if(@$request->sap_connection_id != ''){
                $data->where('sap_connection_id',@$request->sap_connection_id);
            }

            $data = $data->limit(50)->groupBy('u_msec')->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->u_msec,
                    "text" => $value->u_msec
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
                    "text" => $value->u_subsector
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
                    "text" => $value->u_province,
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

            if(@$request->sap_connection_id != ''){
                $data->where('sap_connection_id',@$request->sap_connection_id);
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

            $data = Customer::where('is_active', true)->orderby('u_class','asc')->select('u_class')->limit(50)->groupBy('u_class');

            if($search != ''){
                $data->where('u_class', 'like', '%' .$search . '%');
            }

            if(@$request->sap_connection_id != ''){
                $data->where('sap_connection_id',@$request->sap_connection_id);
            }

            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->u_class,
                    "text" => $value->u_class,
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
                                ->select('id','group_name')
                                ->limit(50);

            if($search != ''){
                $data->where('group_name', 'like', '%' .$search . '%');
            }

            if($request->sap_connection_id != ''){
                $data->where('sap_connection_id',$request->sap_connection_id);
            }

            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->id,
                    "text" => $value->group_name
                );
            }
        }

        return response()->json($response);
    }
}
