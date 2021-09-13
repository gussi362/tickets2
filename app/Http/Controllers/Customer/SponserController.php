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

        return $this->getSuccessResponse('retrieved sponsers successfully' ,$sponsers);

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
        $validator = $request->validate([
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'image' => 'required',
            'event_id' => 'required'
        ]);
        try
        {
            $data = $request->all();
            $data['created_by'] = auth()->user()->id;
            $sponser = Sponser::create($data);

            $this->changeEventSponserStatus($request->input('event_id'));//update events column
        
            return $this->getSuccessResponse('sponser created successfully' ,$sponser);

            
        }catch(\Exception $e)
        {
            return $this->getErrorResponse('exception error' ,$e->getMessage());
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
        return $this->getSuccessResponse('found sponser' ,$sponser);
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
        $company= Sponser::findorfail($id);

        foreach ($request->all() as $key => $value) {
            //if ($value->$key) {
            if ($value) {
                $sponser->$key = $value;
            }

        }

        if($sponser->update())
        {
            return $this->getSucessResponse('updated sponser successfully',$sponser);
        }else
        {
            return $this->getErrorResponse('failed to update sponser with id '.$id);
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
            return $this->getSuccessResponse('deleted sponser successfully' ,$sponser);
        }else
        {
            return $this->getErrorResponse('failed to delete sponser with id '.$id);
        }
    }
}
