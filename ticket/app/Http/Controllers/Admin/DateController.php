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
        $date = Date::orderBy('date','asc')->with('event')->get();
        $data = [
            'responseCode'=>100,
            'responseMessage'=>'retrieved event successful',
            'data'=>['event'=>$date]];
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
       $validator = Validator::make($request->all(),[
            'event_id' => 'required',
            'date' => 'required|date',
            'created_by' => 'required|string'
        ]);

        if ($validator->fails()) {
            $data = ['responseCode'=>102,
                     'responseMessage'=>'not all fields were entered'];
            return response()->json($data);
        }
        $data = $request->all();
        $date = Date::create($data);
        if($date->exists())
        {
            $data = [
                'responseCode'=>100,
                'responseMessage'=>'created date successfully',
                'data'=>['date'=>$date]];

            return response()->json($data);
        }else
        {
            $data = [
                'responseCode'=>102,
                'responseMessage'=>'failed to create date',
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
        $date = Date::findorfail($id);
        $data = ['responseCode'=>100,
        'responseMessage'=>'date found',
        'data'=>['date'=>$date]];
        
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
        $date= Date::find($id);
        
        if($date->update($request->all()))
        {
            $data = ['responseCode'=>100,
                     'responseMessage'=>'updated date successfully',
                     'data'=>['date'=>$date]];
                     
            return response()->json($data);
        }else
        {

            $data = ['responseCode'=>102,
                     'responseMessage'=>'failed to update date with id '.$id,
                     'data'=>['date'=>$date]];
        }                
            return response()->json($data);
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
            $data = ['responseCode'=>100,
                     'responseMessage'=>'deleted Date',
                      'data'=>['date'=>$date]];
            return response()->json($data);
        }else
        {
            
            $data = ['responseCode'=>102,
                     'responseMessage'=>'failed to delete Date with id '.$id,
                      'data'=>['date'=>$date]];
        }
    }
}
