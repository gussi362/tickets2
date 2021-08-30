<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Str;
use App\Models\OrderDetails;
use App\Models\OrderStatus;
use App\Http\Controllers\Controller;
use Validator;
class OrderController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $order = Order::orderBy('name','asc')->with('date')->get();
        $data = [
            'responseCode'=>100,
            'responseMessage'=>'retrieved order successful',
            'data'=>['order'=>$order]];
        return response()->json($data);
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
        
            'ticket_id' => 'required',
            'date_id' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'count' => 'required',

        ]);

        if ($validator->fails()) {
            $data = ['responseCode'=>102,
                     'responseMessage'=>'not all fields were entered'];
            return response()->json($data);
        }
       
        //when ordering 
        //an order details is added with number of tickets under order
        //an order status is added with pending flag

        if($request->input('count') > $this->getTicketsLeft($this->getEventId($request->input('ticket_id')),$request->input('ticket_id')))
        {
            return [['status'=>'error ,not enough tickets available tickets = '.$this->getTicketsLeft($this->getEventId($request->input('ticket_id')),$request->input('ticket_id'))],422];

        }else
        {
            DB::beginTransaction();
            try
            {
                $data = $request->all();
                $data['code'] = $this->generateRandom();;
                $data['id'] =  Str::uuid();
                $data['amount'] = $this->getTotal($this->getTicketPrice($request->input('ticket_id')) ,$request->input('count'));
                $order=Order::create($data);

                for ($i=1 ;$i<=$request->input('count'); $i+=1)
                {
                    $serial = ($order->code."$i");
                    $order_data = ['serial'=>$serial ,
                                    'ticket_id'=>$request->input('ticket_id'),
                                    'price'=>$this->getTicketPrice($request->input('ticket_id')),
                                    'status'=>'false'];
                    
                    OrderDetails::create($order_data);
                    
                }
                 //add order status here 
                 $status_data = [
                    'order_id'=>$data['id']
                 ];//don't need to add type_id cause the default is (1=pending) only when changing status
                
                 OrderStatus::create($status_data);

                DB::commit();
                $return_data = [
                    'order'=>$order,
                ];
                return [
                        'responseCode'=>100,
                        'responseMessage'=>'Order created Successfully',
                        'data'=>$return_data
                ];
                
                
            } catch (\Exception $e) 
            {
                DB::rollback();
                return [
                    'responseCode'=>102,
                    'responseMessage'=>'Failed to create order',
                    'data'=>$e
            ];
            }
        }
        
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
        $order= Order::find($id);
        
        if($order->update($request->all()))
        {
            $data = ['responseCode'=>100,
                     'responseMessage'=>'updated order successfully',
                     'data'=>['order'=>$order]];
                     
            return response()->json($data);
        }else
        {

            $data = ['responseCode'=>102,
                     'responseMessage'=>'failed to update order with id '.$id,
                     'data'=>['event'=>$event]];
                     
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
        $task = Order::findorFail($id);
        if($task->delete())
        {
            return [['status'=>'success'],200];
        }else
        {
            return [['status'=>'fail'],422];
        }
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
    private function generateRandom(){
        $len = 8 ;
        $x = '';
            for ($i = 0 ; $i < $len ; $i ++)
            {
                $x .= intval(rand(0,9));
            }
        return $x;
        }
}
