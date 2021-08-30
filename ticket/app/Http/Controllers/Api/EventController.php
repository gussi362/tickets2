<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Validator;
class EventController extends Controller
{

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

  

}
