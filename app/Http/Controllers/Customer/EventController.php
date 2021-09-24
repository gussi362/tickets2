<?php

namespace App\Http\Controllers\Customer;

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
        $event = Event::orderBy('name_en','asc')->where('company_id',auth()->user()->company_id)->get();
        return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.model.event')]),$event);
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
            'coordinates' => 'required|string'
        ]);

        if ($validator->fails()) 
        {
            return $this->getErrorResponse(trans('messages.errors.input_data'),$validator->errors(),410);
        }
        
        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id;
        $data['created_by'] = auth()->user()->id;
        $event = Event::create($data);
        if($event->exists())
        {
            broadcast(new EventAdded($event));
            broadcast(new overviewChanged($event));

            return $this->getSuccessResponse(trans('messages.generic.successfully_added_new' ,['new' => trans('messages.model.event')]),$event);
        }else
        {
            return $this->getErrorResponse(trans('messages.errors.system_error'),'',501);
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
        if($event->company_id != auth()->user()->company_id)
        {
            return $this->getErrorResponse(trans('messages.errors.unauthorized_opreation'),'',401);
        }
        
        return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.model.event')]),$event);
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
        $event= Event::findorfail($id);
        
        if($event->company_id != auth()->user()->company_id)
        {
            return $this->getErrorResponse(trans('messages.errors.unauthorized_opreation'),'',401);
        }

        if($event->update($request->all()))
        {
            return $this->getSuccessResponse(trans('messages.generic.successfully_updated' ,['new' => trans('messages.model.event')]),$event);
        }else
        {
            return $this->getErrorResponse(trans('messages.errors.system_error'),'',502);
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
        
        if($task->company_id != auth()->user()->company_id)
        {
            return $this->getErrorResponse(trans('messages.errors.unauthorized_opreation'),'',401);
        }

        if($task->delete())
        {
            broadcast(new EventDeleted());
            return $this->getSuccessResponse(trans('messages.generic.successfully_deleted' ,['new' => trans('messages.model.event')]),$task);
        }else
        {
            return $this->getErrorResponse(trans('messages.errors.system_error'),'',503);
        }
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
                        ->where('company_id',auth()->user()->company_id)
                        ->where('last_date','>=',$todayDate)
                        ->with('companyName','ticketCount','date','ticket','sponser')
                        ->select('id','name_ar','name_en','details_ar','details_en','first_date','last_date','coordinates','image')
                        ->get();
                        
                        return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.model.event')]),$event);
     }


}
