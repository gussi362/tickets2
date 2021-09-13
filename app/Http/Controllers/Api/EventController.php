<?php

namespace App\Http\Controllers\Api;

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
