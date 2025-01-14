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
        $api_urls = SapApiUrl::get();
        return view('sap-connection.index', compact('api_urls'));
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

            // Solid Trend
            if(@$connection->id == 1){
                unset($input['company_name']);
                $obj = SapConnection::find(5);
                $obj->fill($input)->save();
            }

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
        $edit = SapConnection::where('id', '!=', 5)->where('id', $id)->firstOrFail();

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
        $data = SapConnection::where('id', '!=', 5)->where('id', $id)->firstOrFail();
        $result = $this->authAPI($data, true);

        return $result;
    }

    public function  checkAPI($id){
        $data = SapConnection::where('id', '!=', 5)->where('id', $id)->firstOrFail();
        $result = $this->authAPI($data, false);

        return $result;
    }

    private function authAPI($data, $sendMail){
        try {

            $testAPI = new SAPTestAPI($data->db_name, $data->user_name, $data->password);

            $result = $testAPI->checkLogin($sendMail);

            $response = ['status' => $result['status'], 'message' => $result['message']];
        } catch (\Exception $e) {
            $response = ['status' => false, 'message' => 'Something went wrong !'];
        }

        add_log(58, $data->toArray());

        return $response;
    }

    public function getAll(Request $request){

        $data = SapConnection::where('id', '!=', 5);

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


    public function fetchHostsUrl(Request $request){

        $data = SapApiUrl::query();

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('url', function($row) {
                                return $row->url;
                            })
                            ->addColumn('status', function($row) {
                                return ($row->active === 1) ? '<span class="badge rounded-pill bg-success">Active</span>' : '<span class="badge rounded-pill bg-danger">Inactive</span>';
                            })
                            ->addColumn('action', function($row) {
                                $btn = '
                                        <a href="#" class="btn btn-icon btn-bg-dark btn-active-color-warning btn-sm mr-5 edit_host-url" data-url="' . route('sap-connection.edit',$row->id). '">
                                            <i class="fas fa-pencil"></i>
                                        </a>
                                        <a href="#" class="btn btn-icon btn-bg-dark btn-active-color-danger btn-sm mr-5 delete_host-url" data-url="' . route('sap-connection.edit',$row->id). '">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        ';
                                return $btn;
                            })
                            ->rawColumns(['status', 'action'])
                            ->make(true);
    }

    public function addHostUrl(Request $request){
        $request->validate([
            'url'    => 'required|url|unique:sap_api_urls,url',
            // 'status' => 'required|in:0,1', 
            ]);

            SapApiUrl::insert(['url' => $request->url, 'active' => 0]);

        return ['status'=> true, 'message'=> 'Host url added successfully!'];;
    }

    public function updateHostUrl(Request $request, $id){
        $request->validate([
            'url'    => 'required|url|unique:sap_api_urls,url,'. $id,
            'status' => 'required|in:0,1', 
            ]);

        $host = SapApiUrl::findOrFail($id);
        if($request->status == 1){
            SapApiUrl::where('id', '!=', $host->id)->update(['active' => false]);
        }
        $host->update(['url' => $request->url, 'active' => $request->status]);

        return ['status'=> true,'message'=>'Host url updated successfully!'];
    }

    public function deleteHostUrl($id){
        $host = SapApiUrl::findOrFail($id);
        $host->delete();

        return ['status' => true, 'message' => 'Host url deleted successfully!', 'data' => [] ];
    }


}
