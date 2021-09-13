<?php

namespace App\Http\Controllers\Customer;

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
        
        return $this->getSuccessResponse('retrieved date successfully',$date);
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
            return $this->getErrorResponse('not all fields were entered');
        }
        
        $data = $request->all();
        $data['created_by'] = auth()->user()->id;
        $date = Date::create($data);

        if($date->exists())
        {
            return $this->getSuccessResponse('created date successfully',$date);
        }else
        {
            return $this->getErrorResposne('failed to create date');
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
        
        return $this->getSuccessResponse('date found',$date);
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
            return $this->getSuccessResponse('updated date successfully',$date);
        }else
        {
            return $this->getErrorResponse('failed to update date with id '.$id);
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
            return $this->getSuccessResponse('deleted date with id '.$id);
        }else
        {
            return $this->getErrorResposne('failed to delete date with id '.$id);
        }
    }
}
