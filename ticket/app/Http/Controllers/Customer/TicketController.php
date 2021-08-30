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
        return Ticket::orderBy('id','asc')->with('event')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
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
            'status' => 'required',
            'details_ar' => 'required|string',
            'details_en' => 'required|string'
        ]);

        if ($validator->fails()) {
            $data = ['responseCode'=>102,
                     'responseMessage'=>'not all fields were entered'];
            return response()->json($data);
        }

        $data = $request->all();
        $ticket = Ticket::create($data);
        if($ticket->exists())
        {
            $data = [
                'responseCode'=>100,
                'responseMessage'=>'created ticket successfully',
                'data'=>['ticket'=>$ticket]];

            return response()->json($data);
        }else
        {
            $data = [
                'responseCode'=>102,
                'responseMessage'=>'failed to create ticket',
                    ];
                    
            return response()->json($data);
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
        $data = ['responseCode'=>100,
        'responseMessage'=>'ticket found',
        'data'=>['ticket'=>$ticket]];
        
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
        $ticket= Ticket::find($id);
        
        if($ticket->update($request->all()))
        {
            $data = ['responseCode'=>100,
                     'responseMessage'=>'updated ticket successfully',
                     'data'=>['ticket'=>$ticket]];
                     
            return response()->json($data);
        }else
        {

            $data = ['responseCode'=>102,
                     'responseMessage'=>'failed to update ticket with id '.$id,
                     'data'=>['ticket'=>$ticket]];
                     
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
        $ticket = Ticket::findorFail($id);
        if($ticket->delete())
        {
            $data = ['responseCode'=>100,
                     'responseMessage'=>'deleted ticket',
                      'data'=>['ticket'=>$ticket]];
            return response()->json($data);
        }else
        {
            
            $data = ['responseCode'=>102,
                     'responseMessage'=>'failed to delete ticket with id '.$id,
                      'data'=>['ticket'=>$ticket]];
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
