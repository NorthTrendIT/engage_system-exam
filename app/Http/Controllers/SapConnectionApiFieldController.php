<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Support\SAPApiField;
use App\Jobs\SyncSapApiField;
use App\Models\SapConnection;
use App\Models\SapConnectionApiField;
use DataTables;
use Validator;
use Auth;

class SapConnectionApiFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = SapConnection::all();
        return view('sap-connection-api-field.index', compact('company'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company = SapConnection::all();
        return view('sap-connection-api-field.add', compact('company'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $rules = array(
                    'field' => 'required|max:185',
                    'sap_field_id' => 'required|max:185',
                    'sap_table_name' => 'required|max:185',
                    'sap_connection_id' => 'required|exists:sap_connections,id',
                );

        if(isset($input['id'])){
            $rules['field'] = 'required|string|max:185';
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            $input['real_sap_connection_id'] = $input['sap_connection_id'];
            if($input['sap_connection_id'] == 5){
                $input['real_sap_connection_id'] = 1;
            }

            if(isset($input['id'])){
                $check = SapConnectionApiField::where('field', $input['field'])->where('sap_connection_id', $input['sap_connection_id'])->where('id', '!=', $input['id'])->first();
                if(!is_null($check)){
                    return $response = ['status' => false, 'message' => 'The given field and field id already used.'];
                }
            }else{
                $check = SapConnectionApiField::where('field', $input['field'])->where('sap_connection_id', $input['sap_connection_id'])->first();
                if(!is_null($check)){
                    return $response = ['status' => false, 'message' => 'The given field and field id already used.'];
                }
            }


            if(isset($input['id'])){
                $connection = SapConnectionApiField::find($input['id']);
                $message = "Sap Connection Api Field details updated successfully.";

                // add_log(56, $input);
            }else{
                $connection = new SapConnectionApiField();
                $message = "Sap Connection Api Field created successfully.";

                // add_log(55, $input);
            }

            $connection->fill($input)->save();

            /*// Solid Trend
            if(@$connection->id == 1){
                unset($input['company_name']);
                $obj = SapConnectionApiField::find(5);
                $obj->fill($input)->save();
            }*/

            $response = ['status'=>true,'message'=>$message];
        }
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edit = SapConnectionApiField::where('id', $id)->firstOrFail();
        $company = SapConnection::all();

        return view('sap-connection-api-field.add', compact('edit', 'company'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function syncAll(){
        try {

            $data = SapConnectionApiField::all();
            foreach ($data as $value) {

                // $sap = new \App\Support\SAPApiField($value->sap_connection->db_name, $value->sap_connection->user_name, $value->sap_connection->password);
                // dd($sap->addApiFieldInDatabase($value));

                $sap_connection = $value->sap_connection;

                $log_id = add_sap_log([
                                'ip_address' => userip(),
                                'activity_id' => 60,
                                'user_id' => userid(),
                                'data' => null,
                                'type' => "S",
                                'status' => "in progress",
                                'sap_connection_id' => $sap_connection->id,
                            ]);

                // Save Data of API Field in database
                SyncSapApiField::dispatch($sap_connection->db_name, $sap_connection->user_name , $sap_connection->password, $value, $log_id);
            }

            $response = ['status' => true, 'message' => 'Sync SAP Connection API Fields Successfully !'];
        } catch (\Exception $e) {
            $response = ['status' => false, 'message' => 'Something went wrong !'];
        }
        return $response;
    }

    public function syncSpecific(Request $request){

        $input = $request->all();

        $rules = array(
                    'id' => 'required|exists:sap_connection_api_fields,id',
                );
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            try {
                $data = SapConnectionApiField::find($request->id);

                $sap = new SAPApiField($data->sap_connection->db_name, $data->sap_connection->user_name, $data->sap_connection->password);
                $sap->addApiFieldInDatabase($data);

                $response = ['status' => true, 'message' => 'Sync SAP Connection API Field Data Successfully !'];
            } catch (\Exception $e) {
                $response = ['status' => false, 'message' => 'Something went wrong !'];
            }
        }

        return $response;
    }

    public function getAll(Request $request){

        $data = SapConnectionApiField::query();

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('field','LIKE',"%".$request->filter_search."%");
                $q->orwhere('sap_field_id','LIKE',"%".$request->filter_search."%");
                $q->orwhere('sap_table_name','LIKE',"%".$request->filter_search."%");
            });
        }

        if($request->filter_company != ""){
            $data->where('sap_connection_id', $request->filter_company);
        }

        if($request->filter_field != ""){
            $data->where('field', $request->filter_field);
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('field', function($row) {
                                return SapConnectionApiField::$fields[$row->field] ?? "-";
                            })
                            ->addColumn('sap_field_id', function($row) {
                                return $row->sap_field_id;
                            })
                            ->addColumn('sap_table_name', function($row) {
                                return $row->sap_table_name;
                            })
                            ->addColumn('company', function($row) {
                                return @$row->sap_connection->company_name;
                            })
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('sap-connection-api-field.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10">
                                            <i class="fa fa-pencil"></i>
                                        </a>';

                                $btn .= '<a href="javascript:" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10 sync-details" data-id="'.$row->id.'" title="Sync details">
                                            <i class="fa fa-sync"></i>
                                        </a>';

                                return $btn;
                            })
                            ->orderColumn('field', function ($query, $order) {
                                $query->orderBy('field', $order);
                            })
                            ->orderColumn('sap_field_id', function ($query, $order) {
                                $query->orderBy('sap_field_id', $order);
                            })
                            ->orderColumn('sap_table_name', function ($query, $order) {
                                $query->orderBy('sap_table_name', $order);
                            })
                            ->orderColumn('company', function ($query, $order) {
                                $query->join('sap_connections', 'sap_connection_api_fields.sap_connection_id', '=', 'sap_connections.id')->orderBy('sap_connections.company_name', $order);
                            })
                            ->rawColumns(['connection', 'action'])
                            ->make(true);
    }

}
