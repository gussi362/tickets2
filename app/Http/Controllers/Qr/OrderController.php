<?php

namespace App\Http\Controllers\Qr;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Str;
use App\Models\OrderDetails;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Ticket;
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
        // return $this->getSuccessResponse('checkedIn',$order." ssss ".$serial );
        
        
        if($this->isPaid($order))
        {
          if(OrderDetails::where('serial','like',$serial.'%')->update(['status'=>'true']))
          {
            $order = OrderDetails::where('serial',$serial)->get();
            return $this->getSuccessResponse('checked in ',$order);
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
  public function getOrder($id )
  {
    $order = Order::find($id);
    return $order;
  }

  public function isPaid($id)
  {
      $sucess_type = ttype::where('name_en','=','success')->first()->id;
      $order = $this->getOrder($id)->type_id;
      
      if($order == $sucess_type)
      {
        return true;
      }else
      {
        return false;
      }
      //return $order;
  }

  /**
   * get checkedIn list for the event based on company id
   * return last checkd in users [serial ,time ,event_name]
   */
  public function getCheckedIn($company_id)
  {
    //this can be done better by using the serial but the issue is the query of getting it 
    //and avoiding a for loop in the $checkedIn query seems to be the best option right now
    $events = Event::where('company_id',$company_id)
                    ->where('status','true')
                    ->pluck('id');//get events which belongs to the company 

    $tickets = Ticket::whereIn('event_id',$events)->pluck('id'); //get the tickets for the events

    $checkedIn = OrderDetails::whereIn('ticket_id',$tickets)
                              ->where('status','true')
                              ->select('serial','ticket_id','updated_at')
                              ->latest('updated_at')
                              ->limit(5)
                              ->get();//get the latest checkedin from the list based on the ticket id

    return $this->getSuccessResponse(trans('messages.generic.successfully_found'),$checkedIn);//should give :new var an empty string as default in messages

  }
}
