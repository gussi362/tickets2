<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Company;
use Validator;
use App\Http\Controllers\Controller;
class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = Company::orderBy('name_en','asc')->get();
        $data = [
            'responseCode'=>100,
            'responseMessage'=>'retrieved company successful',
            'data'=>['company'=>$company]];
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
        
        $validator =  Validator::make($request->all(),[
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'admin' => 'required|string',
            'phone' => 'required',
            'address' => 'required|string',
            'status' => 'required',
            'created_by' => 'required'
        ]);

        if ($validator->fails()) {
            $data = ['responseCode'=>102,
                     'responseMessage'=>'not all fields were entered'];
            return response()->json($data);
        }
        $data = $request->all();
        $company = Company::create($data);
        if($company->exists())
        {
            $data = [
                'responseCode'=>100,
                'responseMessage'=>'created company successfully',
                'data'=>['company'=>$company]];

            return response()->json($data);
        }else
        {
            $data = [
                'responseCode'=>102,
                'responseMessage'=>'failed to create company',
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
        $company = Company::findorfail($id);
        $data = ['responseCode'=>100,
        'responseMessage'=>'company found',
        'data'=>['company'=>$company]];
        
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
        $company= Company::find($id);
        
        if($company->update($request->all()))
        {
            $data = ['responseCode'=>100,
                     'responseMessage'=>'updated company successfully',
                     'data'=>['company'=>$company]];
                     
            return response()->json($data);
        }else
        {

            $data = ['responseCode'=>102,
                     'responseMessage'=>'failed to update company with id '.$id,
                     'data'=>['company'=>$company]];
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
        $company = Company::findorFail($id);
        if($company->delete())
        {
            $data = ['responseCode'=>100,
                     'responseMessage'=>'deleted Company',
                      'data'=>['company'=>$company]];
            return response()->json($data);
        }else
        {
            
            $data = ['responseCode'=>102,
                     'responseMessage'=>'failed to delete Company with id '.$id,
                      'data'=>['company'=>$company]];
        }
    }

    //AdminDashboard 

    /**
     * get current companies where they have active events
     */
    public function getCompaniesWithEvents()
    {
        //companies ,thier current events ,where status = true
        $companies = Company::with(['event' => function($query)
        {
            $query->where('status','true');
        }])->get();
        $data = ['responseCode'=>100,
                 'responseMessage'=>'companies with events',
                 'data'=>['companies'=>$companies]];
        return response()->json($data);
    }

    /**
     * get company events with the id $id
     * @param id ,the company id 
     */
    public function getCompanyWithEvents($id)
    {
        //companies ,thier current events ,where status = true
        $company = Company::where('id',$id)
        ->with(['event' => function($query)
        {
            $query->where('status','true');
        }])->get();

        if($company)
        {
            $data = ['responseCode'=>100,
            'responseMessage'=>'companies with events',
            'data'=>['companies'=>$company]];
            return response()->json($data);
        }else
        {
            $data = ['responseCode'=>102,
            'responseMessage'=>'comapny with id '.$id
            ];
            return response()->json($data);
        }
    }
}
