<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SapConnection;
use App\Jobs\SyncVatGroups;
use App\Models\VatGroup;
use DataTables;

class VatGroupController extends Controller
{
    public function index(){
        $company = SapConnection::all();

        return view('vat-group.index', compact('company'));
    }

    public function syncVatGroups(Request $request){
        try {      

            if($request->filter_company != ""){
              $sap_connections = SapConnection::where('id', $request->filter_company)->first();
      
              $log_id = add_sap_log([
                                        'ip_address' => userip(),
                                        'activity_id' => 61,
                                        'user_id' => userid(),
                                        'data' => null,
                                        'type' => "S",
                                        'status' => "in progress",
                                        'sap_connection_id' => $sap_connections->id,
                                    ]);
              SyncVatGroups::dispatch($sap_connections->db_name, $sap_connections->user_name , $sap_connections->password, $log_id);
      
            }else{
              $sap_connections = SapConnection::where('id', '!=', 5)->get();
              foreach ($sap_connections as $value) {
      
                $log_id = add_sap_log([
                                        'ip_address' => userip(),
                                        'activity_id' => 61,
                                        'user_id' => userid(),
                                        'data' => null,
                                        'type' => "S",
                                        'status' => "in progress",
                                        'sap_connection_id' => $value->id,
                                    ]);
      
                // Save Data of VatGroup in database
                SyncVatGroups::dispatch($value->db_name, $value->user_name , $value->password, $log_id);
              }
            }
      
            $response = ['status' => true, 'message' => 'Sync VatGroup successfully !'];
          } catch (\Exception $e) {
            $response = ['status' => false, 'message' => 'Something went wrong !'];
          }
          return $response;

    }


    public function fetchVatGroups(){
        $vat = VatGroup::all();
        
        return DataTables::of($vat)
                          ->addIndexColumn()
                          ->addColumn('business_unit', function($row) {
                            return $row->sap_connection->company_name;
                          })
                          ->addColumn('code', function($row) {
                            return $row->code;
                          })
                          ->addColumn('name', function($row) {
                            return $row->name;
                          })
                          ->addColumn('status', function($row) {
                            $status = ($row->inactive === 'tYES') ? 'Active' : 'Inactive';
                            return $status;
                          })
                          ->addColumn('rate', function($row) {
                            $rate = json_decode($row->vatgroups_lines);
                            return $rate[0]->Rate.'%';
                          })->make(true)
                          ;
    }



}
