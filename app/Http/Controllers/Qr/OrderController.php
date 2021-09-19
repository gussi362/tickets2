<?php

namespace App\Http\Controllers\Qr;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Str;
use App\Models\OrderDetails;
use App\Http\Controllers\Controller;
use App\Models\ttype;
use Validator;
class OrderController extends Controller
{

    //a reserver come checking in then if status is paid
    //then change in orderDetails
    //                                  
  public function checkIn($order,$serial)
  {
    
        //change in orders
        
        
        if($this->isPaid($order))
        {
          if(OrderDetails::where('serial','like',$serial.'%')->update(['status'=>'true']))
          {
            $order = OrderDetails::where('serial',$serial)->get();
            return $this->getSuccessResponse('checked in ',$order,200);
          }else
          {
            return $this->getErrorResponse('unable to check in','',510);
          }
          
        }else
        {
          return $this->getErrorResponse('tickets are not paid for ','',431);
        }
        
       //return $this->getOrderStatus($order);
  }

  //check if paid
  public function getOrder($id)
  {
    $order = Order::find($id);
    return $order;
  }

  public function isPaid($id)
  {
      $sucess_type = ttype::where('name_en','=','success')->first()->id;
      $order = $this->getOrder($id)->where('type_id',$sucess_type)->get();
      
      if(!$order->isEmpty())
      {
        return true;
      }else
      {
        return false;
      }
      //return $order;
  }
}
