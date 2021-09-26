<?php

namespace App\Http\Controllers\Admin;

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
        return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.model.order')]),$order);
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
        return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.model.order')]),$order);
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
            return $this->getSuccessResponse(trans('messages.generic.successfully_updated' ,['new' => trans('messages.model.order')]),$order);
        }else
        {
            return $this->getErrorResponse(trans('messages.errors.system_error'),'',502);
        }
    }

}
