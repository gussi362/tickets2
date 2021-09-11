<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Str;
use App\Models\OrderDetails;
use App\Models\Ticket;
use App\Http\Controllers\Controller;
use Validator;

use App\Events\OrderAdded;
use App\Events\Dashboard\Customer\overviewChanged;

class OrderController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //TODO::return with order details
        $order = Order::orderBy('created_at','desc')->with('date')->get();
        
        return $this->getSuccessResponse('retrieved order successfully' ,$order);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all() ,[
        
            'ticket' => 'required',
            'date_id' => 'required',
            'name' => 'required',
            'phone' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return $this->getErrorResponse('not all fields were entered');
        }
       
        //when ordering 
        //an order details is added with number of tickets under order
        //an order status is added with pending flag
        $tickets = json_decode($request->ticket);
       // if($request->count > $this->getTicketsLeft($this->getEventId($request->ticket_id),$request->ticket_id))
        $j=0;
       for($i=1 ;$i<=count($tickets); $i+=1)
       {
            if(!$this->isThereEnoughTickets($tickets[$i]->ticket_id,$tickets[$i]->count))
            {
                //return [['status'=>'error ,not enough tickets available tickets = '.$this->getTicketsLeft($this->getEventId($request->input('ticket_id')),$request->input('ticket_id'))],422];
                return $this->getErrorResponse('error ,not enough ticket '.$tickets[$i]->ticket_id.' available tickets = '.$this->getLeftTicketsAmount($tickets[$i]->ticket_id));

            }else
            {
                DB::beginTransaction();
                try
                {
                    
                    $data = $request->all();
                    $data['code'] = $this->generateRandom();;
                    $data['id'] =  Str::uuid();
                    $data['amount'] = $this->getTotal($this->getTicketPrice($tickets[$i]->ticket_id) ,$tickets[$i]->count);
                    
                    $order=Order::create($data);

                    $this->setOrderedTicketsAmount($tickets[$i]->ticket_id ,($tickets[$i]->count + $this->getOrderedTicketsAmount($tickets[$i]->count)));
                    $this->createOrderDetails($tickets[$i]->count ,$order->code ,$tickets[$i]->ticket_id);
                    
                    DB::commit();

                    broadcast(new OrderAdded($order));
                    broadcast(new overviewChanged($order));
                    return $this->getSuccessResponse('created order successfully' ,$order);
                    
                    
                } catch (\Exception $e) 
                {
                    DB::rollback();
                    return $this->getErrorResponse('failed to create order' ,$e->getMessage());

                }
            }
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
     * @param amount number of new tickets
     * @param ticket_id 
     */
    public function setOrderedTicketsAmount($ticket_id ,$amount)
    {
        $ticket= Ticket::where('id',$ticket_id)->first();
        $ticket->update(['ordered' => $amount]);
        
        return $ticket->ordered;
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
     * @param count number of seats for this ticket on order with code
     * @param code unique random number of order
     * @param ticket_id 
     */
    public function createOrderDetails($count ,$code ,$ticket_id)
    {
        for ($i=1 ;$i<=$count; $i+=1)
        {
            $this->createOrderDetail($code."$i" ,$ticket_id);
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::findorfail($id);
        $data = ['responseCode'=>100,
        'responseMessage'=>'order found',
        'data'=>['order'=>$order]];
        
        return response()->json($data);
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
        $order= Order::findorfail($id);

        foreach ($request->all() as $key => $value) 
        {
            //if ($value->$key) {
            if ($value) {
                $order->$key = $value;
            }
        }

        if($order->update())
        {
            return $this->getSuccessResponse('updated order successfully' ,$order);
        }else
        {
            return response()->json($order);
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
        $order = Order::findorFail($id);
        if($this->destroyOrderDetails($order->code) && $order->delete())
        {
            return $this->getSuccessResponse('deleted order successfully' ,$order);
        }else
        {
            return $this->getErrorResponse('failed to deleted order with '.$id);
        }
    }

        /**
     * Remove details of order
     *
     * @param  int  $code
     */
    public function destroyOrderDetails($code)
    {
        OrderDetails::where('serial','like',$serial.'%')->delete();
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
