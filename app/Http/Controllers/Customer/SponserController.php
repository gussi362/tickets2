<?php

namespace App\Http\Controllers\Customer;

use App\Models\Sponser;
use Illuminate\Http\Request;


use Validator;
use DB;
use App\Http\Controllers\Controller;
class SponserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sponsers = Sponser::orderBy('id','asc')->get();

        return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.model.sponser')]),$sponsers,200);

    }


    //TODO: Override validator return message 
    public function messages()
    {
        return [
                    'msg'=>'enter all fields'
        ];
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
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'image' => 'required',
            'event_id' => 'required'
        ]);

        if( $validator->fails() )
        {
            return $this->getErrorResponse(trans('messages.errors.input_data'),$validator->errors(),410);
        }
        try
        {
            $data = $request->all();
            $data['created_by'] = auth()->user()->id;
            $sponser = Sponser::create($data);

            $this->changeEventSponserStatus($request->input('event_id'));//update events column
        
            return $this->getSuccessResponse(trans('messages.generic.successfully_added_new' ,['new' => trans('messages.model.sponser')]),$sponser,201);

            
        }catch(\Exception $e)
        {
            return $this->getErrorResponse(trans('messages.errors.system_error'),$e->getMessage(),501);
        }
        
    }

    //when inserting a new sponser we change the status of sponser from 0 to 1 
    //in case of an existing sponser it does the same nothing change much if sponser of even >1
    private function changeEventSponserStatus($event_id)
    {
        DB::table('events')->where('id','=',$event_id)
                           ->update(['sponser'=>1]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sponser  $sponser
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sponser = Sponser::findorfail($id);
        return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.model.sponser')]),$sponser,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sponser  $sponser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $sponser= Sponser::findorfail($id);

        foreach ($request->all() as $key => $value) {
            //if ($value->$key) {
            if ($value) {
                $sponser->$key = $value;
            }

        }

        if($sponser->update())
        {
            return $this->getSuccessResponse(trans('messages.generic.successfully_updated' ,['new' => trans('messages.model.sponser')]),$sponser,202);
        }else
        {
            return $this->getErrorResponse(trans('messages.errors.system_error'),'',502);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sponser  $sponser
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sponser = Sponser::findorFail($id);
        if($sponser->delete())
        {
            return $this->getSuccessResponse(trans('messages.generic.successfully_deleted' ,['new' => trans('messages.model.sponser')]),$sponser,203);
        }else
        {
            return $this->getErrorResponse(trans('messages.errors.system_error'),'',503);
        }
    }
}
