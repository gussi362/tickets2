<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Validator;
//events 
use App\Events\EventAdded;
use App\Events\EventDeleted;
use App\Events\Dashobard\Admin\overviewChanged;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $event = Event::orderBy('name_en','asc')->get();
        $data = [
            'responseCode'=>100,
            'responseMessage'=>'retrieved event successful',
            'data'=>['event'=>$event]];
        return response()->json($data);
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
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'details_ar' => 'required|string',
            'details_en' => 'required|string',
            'first_date' => 'required',
            'last_date' => 'required',
            'ticket_count' => 'required',
            'image' => 'required|string',
            'status' => 'required|string|max:5',
            'company_id' => 'required',
            'coordinates' => 'required|string'
        ]);

        if ($validator->fails()) {
            $data = ['responseCode'=>102,
                     'responseMessage'=>'not all fields were entered'];
            return response()->json($data);
        }
        
        $data = $request->all();
        $data['created_by'] = auth()->user()->id;
        $event = Event::create($data);
        if($event->exists())
        {
            $data = [
                'responseCode'=>100,
                'responseMessage'=>'created event successfully',
                'data'=>['event'=>$event]];
                broadcast(new EventAdded($event));
                broadcast(new overviewChanged($event));

            return response()->json($data);
        }else
        {
            $data = [
                'responseCode'=>102,
                'responseMessage'=>'failed to create event',
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
        $event = Event::findorfail($id);
        $data = ['responseCode'=>100,
        'responseMessage'=>'event found',
        'data'=>['event'=>$event]];
        
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
        $event= Event::find($id);
        
        if($event->update($request->all()))
        {
            $data = ['responseCode'=>100,
                     'responseMessage'=>'updated event successfully',
                     'data'=>['event'=>$event]];
                     
            return response()->json($data);
        }else
        {

            $data = ['responseCode'=>102,
                     'responseMessage'=>'failed to update event with id '.$id,
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
        $task = Event::findorFail($id);
        if($task->delete())
        {
            $data = ['responseCode'=>100,
                     'responseMessage'=>'deleted Event',
                      'data'=>['event'=>$task]];
                      broadcast(new EventDeleted());
            return response()->json($data);
        }else
        {
            
            $data = ['responseCode'=>102,
                     'responseMessage'=>'failed to delete Event with id '.$id,
                      'data'=>['event'=>$task]];
        }
    }

    /**
     *  return a list of all the current active events
     *
     * @
     * @param  int  $idreturn \Illuminate\Http\Response
     */
/*
    public function getCurrentEvents()
    {
        $todayDate = date('Y-m-d');
        $events = Event::where('status','=','true')
                       ->where('last_date','>=',$todayDate)
                       ->with('companyName','sponser','ticket','date')
                       ->select('id', 'name_ar', 'name_en','details_en','details_ar', 'company_id', 'sponser')
                       ->paginate(5);    
        return response()->json(['responseCode' => 100, 'responseMessage'=>'Success', 'data'=> $events]);
    }




        //get user company
        //get user company's total event
        //get current event 
        //foreach event -> orders -> total
        //for each order where day=passed get attended & not attended

        
        //   {
        //        total_events:
        //        current_events:
        //                         {
        //                              []event_details,
        //                              order,
        //                              total order,
        //                              []total attend

        //                         }
           
        //   }
         


    public function getEventsDetails()
    {
        $events = $this->getCompanyEvents();
        $current_events = $this->getCompanyCurrentEvents();

        $data = [
            "responseCode"=> 100,
            "responseMessage"=>"sucess",
            "data"=>[
                        "allEvents"=>$events,
                        "currentEvents"=>$current_events]
            ];
        return response()->json($data);
        
    }

    
    public function getCompanyEvents()
    {
        $company_id = auth()->user()->company_id;
        return DB::table('events')->where('company_id','=',$company_id)
                                  ->get();
    }

    public function getCompanyCurrentEvents()
    {
        $company_id = auth()->user()->company_id;

        return DB::table('events')->where('company_id','=',$company_id)
                                  ->where('status','=','true')
                                  ->get();
    }

    public function getCompanyCurrentEventsDetails()
    {
        //event ,total cash of event,total of tickets for each event
        $events = $this->getCompanyCurrentEvents();
        

        $companyId =  \Auth::user()->company_id;

        $events = Event::where('company_id', $companyId)
                    ->withCount('ticketCount')
                    ->get();


        return $events;
        $details = array();
        for($i=0 ;$i<count($events); $i+=1)
        {
            $event = $events[$i];
            $event_id = $event->id;
            $event_name= $event->name_en;
            $total_of_tickets = $event->ticket_count;
            $total_amount = $this->getTotalAmountOfEvent(1);
            
           $tmp = [
                   'event_id'=>$event_id,
                   'event_name'=>$event_name,
                   'ticket_count'=>$total_of_tickets,
                   'total_amount_of_event'=>$total_amount
           ];
           $details[$i] = $tmp;

        }
        $data = [
                'responseCode'=>100,
                'responseMessage'=>'success',
                'data'=>$details
        ];
        return response()->json($data);
    }

    public function getTotalTickets($event_id)
    {
        return DB::table('tickets')->where('event_id',$event_id)->count();
    }

    public function getTotalAmountOfEvent($event_id)
    {
        //getTickets
        //countTickets
        $event = Event::where('id', $event_id)->first();


        return $event;
        $tickets = DB::table('tickets')->where('event_id','=',$event_id)->get();
        $details = array();
        $sum_amount = 0;
        $sum_sold_tickets =0;
        $sum_tickets = 0;
        for($i=0 ;$i<count($tickets); $i+=1){
            $ticket = $tickets[$i];
            $ticket_id = $ticket->id;
            $event_id = $ticket->event_id;
            $total_count = Order::where('ticket_id','=',$ticket_id)->sum('count');
            $total_amount = Order::where('ticket_id','=',$ticket_id)->sum('amount');
           
           $sum_amount += $total_amount;
           $sum_sold_tickets = $total_count;
        }
        $data = [
                "event_total_amount"=>$sum_amount,
                "event_total_sold_tickets"=>$sum_sold_tickets
        ];
        
        return $data;
    }
*/

//DashboardAdmin

    /**
     *  return a list of all the current active events
     *  of all companies
     * @
     * @param  int  $idreturn \Illuminate\Http\Response
     */

     public function getEventsCurrent()
     {
         $todayDate = date('Y-m-d');
         $event = Event::where('status','true')
                        ->where('last_date','>=',$todayDate)
                        ->with('companyName','ticketCount','date','ticket','sponser')
                        ->select('id','name_ar','name_en','details_ar','details_en','first_date','last_date','coordinates','image')
                        ->get();
                        
         $data = ['responseCode'=>100,
          'responseMessage'=>'retrieved current events',
          'data'=>['event'=>$event]];
        
         return response()->json($data);         
     }

    /**
     *  return a list of all the current active events
     *  of company [id] with details
     * @
     * @param  int  $idreturn \Illuminate\Http\Response
     */

    public function getCompanyEvent($id)
    {
        //eventid ,name ,ticketSold ,totalAmount
        $todayDate = date('Y-m-d');
        $events = Event::where('company_id',$id)
                    ->where('status','true')
                    ->where('last_date','>=',$todayDate)
                    ->with('companyName','ticketCount','date','ticket','sponser')
                    ->select('id','name_ar','name_en','details_ar','details_en','first_date','last_date','coordinates','image')
                    ->get();
        
        $data = ['responseCode'=>100,
                 'responseMessage'=>'retrieved current events',
                 'data'=>['event'=>$events]];
       
        return response()->json($data);         
    }

    //all events of company since registering
    public function getCompanyEvents($company_id)
    {//TODO::ADD ATTENDANT TICKETS
        //eventid ,name ,ticketSold ,totalAmount
        $todayDate = date('Y-m-d');
        $events = Event::where('company_id',$company_id)
                    ->with('companyName','ticketCount','date','ticket','sponser')
                    ->select('id','name_ar','name_en','details_ar','details_en','first_date','last_date','coordinates','image')
                    ->get();
        
        $data = ['responseCode'=>100,
                 'responseMessage'=>'retrieved current events',
                 'data'=>['event'=>$events]];
       
        return response()->json($data);         
    }

}
