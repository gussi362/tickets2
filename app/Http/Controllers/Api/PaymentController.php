<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Http\Controllers\Controller;
use App\Models\ttype;

use QrCode;
class PaymentController extends Controller
{
    //return order with status
    public function getOrder($order_id)
    {
        $order = Order::find($order_id);
        return $order;
    }

    public function pay($order_id)
    {
        //make request with order and amount to external api
        if(true)//if payment was successful
        {
            $pending_status = ttype::where('name_en','=','pending')->first()->id;
            $success_status = ttype::where('name_en','=','success')->first()->id;
            $completed_status = ttype::where('name_en','=','completed')->first()->id;
            $serial = Order::find($order_id)->first()->code;
            
            if(Order::find($order_id)->update(['payment'=>$success_status,'status'=>$completed_status]))
            {
                    $order = $this->getOrder($order_id);
                    $order_details = OrderDetails::where('serial','like',$serial.'%')->get();
                    $data = ['responseCode'=>102,
                             'responseMessage'=>'successful payment',
                             'data' => ['order'=>$order,'orderDetails'=>$order_details]
                    ];
                    return $data;
                
              
            }else
            {
              $data = ['responseCode'=>102,
              'responseMessage'=>'failed to pay order',
              ];
              return $data;
            }    

        }else
        {
            $data = ['responseCode'=>102,
                    'responseMessage'=>'tickets not paid'
                    ];
                    return $data;
        }
    }

}
