<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\SAPTestAPI;
use App\Models\Company;
use DataTables;
use Validator;
use Auth;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('company.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('company.add');
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
                    'db_name' => 'required|string|max:185',
                    'password' => 'required|string|max:185',
                );

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            if(isset($input['id'])){
                $company = Company::find($input['id']);
                $message = "Company details updated successfully.";
            }else{
                $company = new Company();
                $message = "New Company created successfully.";
            }

            $company->fill($input)->save();

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
        $edit = Company::findOrFail($id);

        return view('company.add', compact('edit'));
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
        $data = Company::findOrFail($id);
        try {

            $testAPI = new SAPTestAPI($data->db_name, $data->user_name, $data->password);

            $result = $testAPI->checkLogin();

            $response = ['status' => $result['status'], 'message' => $result['message']];
        } catch (\Exception $e) {
            $response = ['status' => false, 'message' => 'Something went wrong !'];
        }
        return $response;
    }

    public function getAll(Request $request){

        $data = Company::query();

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
                            ->orderColumn('password', function ($query, $order) {
                                $query->orderBy('password', $order);
                            })
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('company.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10">
                                    <i class="fa fa-pencil"></i>
                                  </a>';

                                $btn .= '<a href="javascript:;" data-url="' . route('company.test',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm test-api">
                                    <i class="fa fa-eye"></i>
                                  </a>';

                                return $btn;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
    }
}
