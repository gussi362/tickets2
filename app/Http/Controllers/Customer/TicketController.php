<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Order;
use DB;
use Validator;

use App\Http\Controllers\Controller;
class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ticket = Ticket::orderBy('id','asc')->with('event')->get();

        return $this->getSuccessResponse('retrieved tickets successfully' ,$ticket);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'event_id' => 'required',
            'amount' => 'required',
            'name' => 'required',
            'status' => 'required',
            'details_ar' => 'required|string',
            'details_en' => 'required|string'
        ]);

        if ($validator->fails()) 
        {
            return $this->getErrorResponse('not all fields were entered');
        }

        try
        {
            $data = $request->all();
            $data['created_by'] = auth()->user()->id;
            
            $ticket = Ticket::create($data);
            if($ticket->exists())
            {
                return $this->getSuccessResponse('created ticket successfully' ,$ticket);
            }else
            {
                return $this->getErrorResponse('failed to create ticket');
            }
        }catch(\Exception $e)
        {
            return $this->getErrorResponse('exception error' ,$e->getMessage());
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ticket = Ticket::findorfail($id);

        return $this->getSuccessResponse('ticket found' ,$ticket);
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
        $ticket= Ticket::findorfail($id);
        
        foreach ($request->all() as $key => $value) 
        {
            //if ($value->$key) {
            if ($value) {
                $ticket->$key = $value;
            }
        }
        if($ticket->update())
        {
            return $this->getSuccessResponse('updated ticket successfully' ,$ticket);
        }else
        {            
            return $this->getErrorResponse('failed to update ticket with id '.$id);
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
        $ticket = Ticket::findorFail($id);
        if($ticket->delete())
        {
            return $this->getSuccessResponse('deleted ticket successfully' ,$ticket);
        }else
        {
            return $this->getErrorResponse('failed to delete ticket with id '.$id);
        }
    }

    //dashboard
    

     public function getCompanyTickets()
     {
         $company_id = auth()->user()->company_id;
         //select event
         $tickets = Ticket::with(['event'=>function($query) use($company_id)
         {
             $query->where('company_id','=',$company_id);
         }])
         ->get();

         return $tickets;
     }

     public function getCompanyTicketsDetails()
     {//TODO : REDO THIS
         $tickets = $this->getCompanyTickets();

         $details = array();
         for($i=0 ;$i<count($tickets); $i+=1)
         {
             $ticket = $tickets[$i];
             $ticket_id = $ticket->id;
             $event_id = $ticket->event_id;
             $total_count = Order::where('ticket_id','=',$ticket_id)->sum('count');
             $total_amount = Order::where('ticket_id','=',$ticket_id)->sum('amount');
            $tmp = [
                    'event_id'=>$event_id,
                    'ticket_id'=>$ticket_id,
                    'total_of_tickets'=>$ticket->event->ticket_count,
                    'tickets_sold'=>$total_count,
                    'total_amount'=>$total_amount
            ];
            $details[$i] = $tmp;

         }
         return response()->json($details);
     }
}
