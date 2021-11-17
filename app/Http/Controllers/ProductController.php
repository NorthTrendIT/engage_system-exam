<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SyncProducts;
use App\Models\Product;
use DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('product.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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

    public function syncProducts(){
        try {

            // Save Data of Product in database
            SyncProducts::dispatch('TEST-APBW', 'manager', 'test');

            $response = ['status' => true, 'message' => 'Sync Product successfully !'];
        } catch (\Exception $e) {
            $response = ['status' => false, 'message' => 'Something went wrong !'];
        }
        return $response;
    }

    public function getAll(Request $request){

        $data = Product::query();

        if($request->filter_status != ""){
            $data->where('is_active',$request->filter_status);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('item_code','LIKE',"%".$request->filter_search."%");
                $q->orwhere('item_name','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('item_name', function($row) {
                                return @$row->item_name ?? "";
                            })
                            ->addColumn('item_code', function($row) {
                                return @$row->item_code ?? "";
                            })
                            ->addColumn('status', function($row) {
                                $btn = "";
                                if($row->is_active){
                                    $btn .= '<a href="javascript:" class="btn btn-sm btn-light-success btn-inline status">Active</a>';
                                }else{
                                    $btn .= '<a href="javascript:" class="btn btn-sm btn-light-danger btn-inline status">Inctive</a>';
                                }

                                return $btn;
                            })
                            ->addColumn('class', function($row) {
                                $html = "";
                                return $html;
                            })
                            ->addColumn('created_date', function($row) {
                                return date('M d, Y',strtotime($row->created_date));
                            })
                            ->orderColumn('item_name', function ($query, $order) {
                                $query->orderBy('item_name', $order);
                            })
                            ->orderColumn('item_code', function ($query, $order) {
                                $query->orderBy('item_code', $order);
                            })
                            ->orderColumn('created_date', function ($query, $order) {
                                $query->orderBy('created_date', $order);
                            })
                            ->orderColumn('status', function ($query, $order) {
                                $query->orderBy('is_active', $order);
                            })
                            ->rawColumns(['status'])
                            ->make(true);
    }
}
