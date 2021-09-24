<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Date;
use Validator;

use App\Http\Controllers\Controller;
class DateController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $date = Date::orderBy('date','desc')->with('event')->get();
        
        return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.model.date')]),$date);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $validator = Validator::make($request->all(),[
            'event_id' => 'required',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) 
        {
            return $this->getErrorResponse(trans('messages.errors.input_data'),$validator->errors(),410);
        }

        $data = $request->all();
        $data['created_by'] = auth()->user()->id;
        $date = Date::create($data);
        if($date->exists())
        {
            return $this->getSuccessResponse(trans('messages.generic.successfully_added_new' ,['new' => trans('messages.model.date')]),$date);
        }else
        {
            return $this->getErrorResposne('failed to create date','',501);
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
        $date = Date::findorfail($id);
        
        return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.model.date')]),$date);
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
        $date= Date::findorfail($id);
        
        foreach ($request->all() as $key => $value) {
            //if ($value->$key) {
            if ($value) {
                $date->$key = $value;
            }

        }

        if($date->update())
        {
            return $this->getSuccessResponse(trans('messages.generic.successfully_updated' ,['new' => trans('messages.model.date')]),$date);
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
        $date = Date::findorFail($id);
        if($date->delete())
        {
            return $this->getSuccessResponse(trans('messages.generic.successfully_deleted' ,['new' => trans('messages.model.date')]),$date);
        }else
        {
            return $this->getErrorResponse(trans('messages.errors.system_error'),503);
        }
    }
}
