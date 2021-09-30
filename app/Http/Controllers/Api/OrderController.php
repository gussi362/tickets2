<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Str;
use App\Models\OrderDetails;
use App\Models\Ticket;
use App\Http\Controllers\Controller;
use Validator;

use App\Events\OrderAdded;
use App\Events\Dashboard\Admin\overviewChanged;

class OrderController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  
       /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all() ,[
        
            'ticket' => 'required',//how to check if the form is sent as required {ticket_id ,count}?
            'date_id' => 'required',
            'event_id' => 'required',
            'name' => 'required',
            'phone' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return $this->getErrorResponse(trans('messages.errors.input_data'),$validator->errors(),410);
        }
       
        //when ordering 
        //an order details is added with number of tickets under order
        //an order status is added with pending flag

        $tickets = json_decode($request->ticket);
       
        $total_count  = 0;
        $total_amount = 0;

        //first we check if all tickets have have available quantity if one ticket isn't it returns with error
        for($i=0 ;$i<count($tickets); $i+=1)
        {
            if(!$this->isThereEnoughTickets($tickets[$i]->ticket_id,$tickets[$i]->count))
            {
                return $this->getErrorResponse(trans('messages.errors.insufficient_tickets',['ticket' => $this->getLeftTicketsAmount($tickets[$i]->ticket_id)]),'',430);
                // return $this->getErrorResponse('error ,not enough ticket '.$tickets[$i]->ticket_id.' available tickets = '.$this->getLeftTicketsAmount($tickets[$i]->ticket_id));

            }
        }

        //then we calucate the total of amount ,count
        foreach ($tickets as $ticket)
        {
            $total_amount += $this->getTotal($this->getTicketPrice($ticket->ticket_id) ,$ticket->count);
            $total_count += $ticket->count;
        }

        //then we create the order
        DB::beginTransaction();
        try
        {
            
            $data = $request->all();
            $data['code'] = $this->generateRandom();;
            $data['id'] =  Str::uuid();
            $data['amount'] = $total_amount;
            $data['count'] = $total_count;
            $order=Order::create($data);
            
            
            $this->setOrderedTicketsAmount($tickets);

            $this->createOrderDetails($tickets ,$order->code);
            
             DB::commit();
            broadcast(new OrderAdded($order));
            
            broadcast(new overviewChanged($order));
            
            return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.model.order')]),$order);

            
            
        } catch (\Exception $e) 
        {
            DB::rollback();
            return $this->getErrorResponse(trans('messages.errors.system_error'),$e->getMessage(),501);
        }
    }


    /**
     * get tickets ordered
     * @param ticket_id 
     */    
    public function getOrderedTicketsAmount($ticket_id)
    {
        return Ticket::where('id',$ticket_id)->first()->ordered;
    }

    /**
     * set tickets ordered when creating an order
     * @param tickets contain the ticket_id ,count sent in request 
     */
    public function setOrderedTicketsAmount($tickets)
    {
        foreach ($tickets as $ticket) 
        {
            $ticketa= Ticket::where('id',$ticket->ticket_id)->first();
            $ticketa->ordered = ($ticket->count + $this->getOrderedTicketsAmount($ticket->ticket_id));
            $ticketa->update();    
        }

        return $tickets;
    }

    /**
     * return true when tickets are enough to satisfy the order
     * @param count number of seats for this ticket on order with code
     * 
     * @param ticket_id 
     */
    public function isThereEnoughTickets($ticket_id,$count)
    {
        $ticket = $this->getTotalQuanitiyOfTickets($this->getEventId($ticket_id));
        return ($count <=($ticket - $this->getOrderedTicketsAmount($ticket_id))) ? true :false ;
    }

     /**
     * event.ticket_count - tickets_ordered
     * @param count number of seats for this ticket on order with code
     * @param code unique random number of order
     * @param ticket_id 
     */
    public function getLeftTicketsAmount($ticket_id)
    {
        $ticket = Ticket::where('id',$ticket_id)->first();
        return $ticket->amount - $ticket->ordered;
    }
    
    /**
     * creating order details for order
     * @param tickets sent in request containts ticket_id ,count
     * @param code unique random number of order
     */
    public function createOrderDetails($tickets ,$code )
    {
        $ticket_count =1;
        foreach ($tickets as $ticket ) 
        {
            for ($i=0; $i <$ticket->count ; $i+=1) 
            { 
                $this->createOrderDetail($code."$ticket_count" ,$ticket->ticket_id);
                $ticket_count++;
            }
        }
    }


    /**
     * create order detail
     * @param serial of reservation
     * @param ticket_id
     */
    public function createOrderDetail($serial ,$ticket_id)
    {
        $order_data = [ 'serial'=>$serial ,
                        'ticket_id'=>$ticket_id,
                        'price'=>$this->getTicketPrice($ticket_id),
                        'status'=>'false'];

        OrderDetails::create($order_data);
    }
    //get order total 
    //@amount price of 1ticket
    //@tickets_total number of tickets for this order 
    private function getTotal($amount,$numberOfTickets)
    {
        return $amount * $numberOfTickets;
    }

    //get price of ticket with id
    //should use the eloquent model relationship to access this order ticket ! 
    private function getTicketPrice($ticket_id)
    {
        $query = DB::table('tickets')->where('id',$ticket_id)->first();
        return $query->amount;
    }

        /**
     * Remove details of order
     *
     * @param  int  $code
     */
    public function destroyOrderDetails($code)
    {
        OrderDetails::where('serial','like',$code.'%')->delete();
    }

    /**
     * return the total amount of tickets left for ordering
     * @param event_id 
     * @param ticket_id
     */
    private function getTicketsLeft($event_id,$ticket_id)
    {
        // get total quantity of tickets from event 
      
      // get total of orders 
        //subtract the both of them 
        //event.count is the total of tickets available 
        //sum(order.count) is the total quanitiy of already ordered tickets

        return ($this->getTotalQuanitiyOfTickets($event_id) - $this->getTotalAmountOfOrders($ticket_id));
    }

    /**
     * return the quantity of ticket this event is offering
     * @param event_id
     */
    private function getTotalQuanitiyOfTickets($event_id)
    {
        $event_quan = DB::table('events')->where('id','=',$event_id)->first();

        return $event_quan->ticket_count;
    }

     /**
     * return the amount of tickets that has been distrubed 
     * @param event_id
     */
    private function getTotalAmountOfOrders($ticket_id)
    {
        $tickets_sum = DB::table('orders')->where('ticket_id','=',$ticket_id)->sum('count');

        return $tickets_sum;
    }

    /**
     * return event id of ticket
     * @param ticket_id
     */
    private function getEventId($ticket_id)
    {
        $event = DB::table('tickets')->where('id','=',$ticket_id)->first();

        return $event->event_id;
    }


    /**
     * return a random number of length 8 for order field 
     */
    private function generateRandom()
    {
        $len = 8 ;
        $x = '';
            for ($i = 0 ; $i < $len ; $i ++)
            {
                $x .= intval(rand(0,9));
            }
        return $x;
    }
}
