<?php

namespace App\Http\Controllers\Customer;

use App\Models\OrderStatus;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
class OrderStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $order = OrderStatus::orderBy('id','asc')->get();
        $data = [
            'responseCode'=>100,
            'responseMessage'=>'retrieved order successful',
            'data'=>['order'=>$order]];
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
     * @param  \App\Models\OrderStatus  $orderStatus
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = OrderStatus::findorfail($id);
        $data = ['responseCode'=>100,
        'responseMessage'=>'order found',
        'data'=>['orderStatus'=>$order]];
        
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrderStatus  $orderStatus
     * @return \Illuminate\Http\Response
     */
    public function edit(OrderStatus $orderStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrderStatus  $orderStatus
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order= OrderStatus::find($id);
        
        if($order->update($request->all()))
        {
            $data = ['responseCode'=>100,
                     'responseMessage'=>'updated order status successfully',
                     'data'=>['orderStatus'=>$order]];
                     
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
     * @param  \App\Models\OrderStatus  $orderStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderStatus $orderStatus)
    {
        //
    }
}
