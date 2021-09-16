<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Http\Controllers\Controller;


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
        return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.model.event')]),$event);
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
                        
                        return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.model.event')]),$event);
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
       
                    return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.model.event')]),$event);
    }

    //all events of company since registering
    public function getCompanyEvents($company_id)
    {//TODO ::ADD ATTENDANT TICKETS ,moved to dashboard controller ,saved for legacy reasons
        //eventid ,name ,ticketSold ,totalAmount
        $todayDate = date('Y-m-d');
        $events = Event::where('company_id',$company_id)
                    ->with('companyName','ticketCount','date','ticket','sponser')
                    ->select('id','name_ar','name_en','details_ar','details_en','first_date','last_date','coordinates','image')
                    ->get();
       
                    return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.model.event')]),$event);  
    }

}
