<?php

namespace App\Http\Controllers\Qr;

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
        return $this->getSuccessResponse('retrieved order details ',$order);
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
        return $this->getSuccessResponse('order details ',$order);
    }

}
