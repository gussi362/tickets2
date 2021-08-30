<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Models\OrderDetails;
use App\Http\Controllers\Controller;
class OrderDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $order = OrderDetails::orderBy('id','asc')->get();
        $data = [
            'responseCode'=>100,
            'responseMessage'=>'retrieved order successful',
            'data'=>['orderDetails'=>$order]];
        return response()->json($data);
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
        $order = OrderDetails::findorfail($id);
        $data = ['responseCode'=>100,
        'responseMessage'=>'order found',
        'data'=>['orderDetails'=>$order]];
        
        return response()->json($data);
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
        $order= OrderDetails::find($id);
        
        if($order->update($request->all()))
        {
            $data = ['responseCode'=>100,
                     'responseMessage'=>'updated order details successfully',
                     'data'=>['orderDetails'=>$order]];
                     
            return response()->json($data);
        }else
        {

            $data = ['responseCode'=>102,
                     'responseMessage'=>'failed to update order details with id '.$id,
                     'data'=>['orderDetails'=>$event]];
                     
            return response()->json($data);
        }
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
}
