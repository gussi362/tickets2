<?php

namespace App\Http\Controllers\Api;

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
        return $this->getSuccessResponse('retrieved order details successfully' ,$order);
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
        return $this->getSuccessResponse('retrieved order details successfully' ,$order);
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
        $order= OrderDetails::findorfail($id);
        
        foreach ($request->all() as $key => $value) 
        {
            //if ($value->$key) {
            if ($value) {
                $order->$key = $value;
            }
        }
        if($order->update())
        {
            return $this->getSuccessResponse('updated order details successfully' ,$order);
        }else
        {
                     return $this->getErrorResponse('failed to update order details with id '.$id);
        }
    }

}
