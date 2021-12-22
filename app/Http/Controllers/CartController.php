<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer_id = Auth::user()->customer_id;
        $data = Cart::with(['product', 'customer'])->where('customer_id', $customer_id)->get();
        return view('cart.index', compact('data'));
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

    public function addToCart($id){
        // if(!@Auth::user()->customer->sap_connection_id){
        //     return $response = ['status'=>false,'message'=>"Oops! Customer not found in DataBase."];
        // }

        if(isset($id)){
            $cart = new Cart();
            $cart->customer_id = Auth::user()->customer_id;
            $cart->product_id = $id;
            $cart->qty = 1;
            $cart->save();

            return $response = ['status'=>true,'message'=>"Product added to cart successfully."];
        }

        return $response = ['status'=>false,'message'=>"Something went wrong please try again."];
    }

    public function updateQty(Request $request, $id){
        $data = $request->all();
        if(isset($id) && isset($data['qty'])){
            $cart = Cart::findOrFail($id);
            $cart->qty = $data['qty'];
            $cart->save();

            return $response = ['status'=>true,'message'=>"Product quantity updated successfully."];
        }

        return $response = ['status'=>false,'message'=>"Something went wrong please try again."];
    }

    public function removeFromCart($id){
        if(isset($id)){
            Cart::where('id', $id)->delete();

            return $response = ['status'=>true,'message'=>"Product removed from cart."];
        }

        return $response = ['status'=>false,'message'=>"Something went wrong please try again."];
    }
}
