<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\SAPTestAPI;
use App\Models\SapConnection;
use App\Models\SapApiUrl;
use DataTables;
use Validator;
use Auth;

class SapConnectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('sap-connection.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sap-connection.add');
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
                    'company_name' => 'required|string|max:185',
                    'user_name' => 'required|string|max:185',
                    'db_name' => 'required|string|max:185|unique:sap_connections,db_name',
                    'password' => 'required|string|max:185',
                );

        if(isset($input['id'])){
            $rules['db_name'] = 'required|string|max:185|unique:sap_connections,db_name,'.$input['id'].',id';
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            if(isset($input['id'])){
                $connection = SapConnection::find($input['id']);
                $message = "SAP Connection details updated successfully.";

                add_log(56, $input);
            }else{
                $connection = new SapConnection();
                $message = "SAP Connection created successfully.";

                add_log(55, $input);
            }

            $connection->fill($input)->save();

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
        $edit = SapConnection::findOrFail($id);

        return view('sap-connection.add', compact('edit'));
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

    public function testAPI($id)
    {
        $data = SapConnection::findOrFail($id);
        try {

            $testAPI = new SAPTestAPI($data->db_name, $data->user_name, $data->password);

            $result = $testAPI->checkLogin();

            $response = ['status' => $result['status'], 'message' => $result['message']];
        } catch (\Exception $e) {
            $response = ['status' => false, 'message' => 'Something went wrong !'];
        }

        add_log(58, $data->toArray());

        return $response;
    }

    public function getAll(Request $request){

        $data = SapConnection::query();

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('company_name','LIKE',"%".$request->filter_search."%");
                $q->orwhere('user_name','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('company_name', function($row) {
                                return $row->company_name;
                            })
                            ->addColumn('user_name', function($row) {
                                return $row->user_name;
                            })
                            ->addColumn('db_name', function($row) {
                                return $row->db_name;
                            })
                            ->addColumn('pasword', function($row) {
                                return $row->password;
                            })
                            ->orderColumn('company_name', function ($query, $order) {
                                $query->orderBy('company_name', $order);
                            })
                            ->orderColumn('user_name', function ($query, $order) {
                                $query->orderBy('user_name', $order);
                            })
                            ->orderColumn('db_name', function ($query, $order) {
                                $query->orderBy('db_name', $order);
                            })
                            ->addColumn('connection', function ($row) {
                                $btn = '<a href="javascript:;" data-url="' . route('sap-connection.test',$row->id). '" class="btn btn-bg-light btn-active-color-primary btn-sm test-api">
                                    Test Connection
                                  </a>';
                                return $btn;
                            })
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('sap-connection.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10">
                                    <i class="fa fa-pencil"></i>
                                  </a>';
                                return $btn;
                            })
                            ->rawColumns(['connection', 'action'])
                            ->make(true);
    }


    public function updateApiUrl(Request $request){
        $input = $request->all();

        $rules = array(
                    'url' => 'required|string|max:185|url',
                );

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            
            $obj = SapApiUrl::firstOrNew();

            if($obj->fill($input)->save()){

                add_log(57, $obj->toArray());

                $response = ['status'=>true,'message'=>"API URL updated successfully!"];
            }else{
                $response = ['status'=>false,'message'=>"Something went wrong!"];
            }

        }
        return $response;
    }
}
