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
        return Sponser::orderBy('id','asc')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        
        $data = $request->all();
        Sponser::create($data);
        $this->changeEventSponserStatus($request->input('event_id'));//update events column
       
        $return_data = [
            'responseCode'=>100,
            'responseMessage'=>'Sponser added successfully',
        ];
        return $return_data; 
        
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
        return Sponser::findorfail($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sponser  $sponser
     * @return \Illuminate\Http\Response
     */
    public function edit(Sponser $sponser)
    {
        //
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
        $company= Sponser::find($id);
        $company->update($request->all());
        return [
            'responseCode'=>100,
            'responseMessage'=>'Updated sponser',
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sponser  $sponser
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Sponser::findorFail($id);
        if($task->delete())
        {
            return  [
                'responseCode'=>100,
                'responseMessage'=>'Sponser deleted',
            ];
        }else
        {
            return  [
                'responseCode'=>102,
                'responseMessage'=>'Failed to delete sponser',
            ];
        }
    }
}
