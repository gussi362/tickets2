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
        
        //don't create vars if not necessaray ,they take memory and 
        // $data = ['responseCode'=>100,
        //     'responseMessage'=>'',
        //     'data'=>['company'=>$company]];
        return $this->getSuccessResponse('retrieved company successful',$company);

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

        if ($validator->fails()) 
        {
            return $this->getErrorResponse('not all fields were entered');
        }
        try{

            $company = Company::create($request->all());
            if($company->exists())
            {
                return $this->getSuccessResponse('created company successfully',$company);
            }else
            {           
                return $this->getErrorResponse('failed to create company');
            }
        }catch(\Exception $e)
        {
            return $this->getErrorResponse('exception error',$e->getMessage());
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
        return $this->getSuccessResponse('company found',$company);
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
        $company= Company::findorfail($id);
        

        foreach ($request->all() as $key => $value) {
            //if ($value->$key) {
            if ($value) {
                $company->$key = $value;
            }

        }

        if($company->update())
        {
            return $this->getSuccessResponse('updated company successfully',$company);
        }else
        {
            return $this->getErrorResponse('failed to update company with id '.$id);
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
        $company = Company::findorFail($id);
        if($company->delete())
        {
            return $this->getSuccessResponse('deleted company',$company);
        }else
        {
            return $this->getErrorResponse('failed to delete Company with id '.$id);
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

        return $this->getSuccessResponse('companies with events',$companies);
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
            return $this->getSuccessResponse('companies with events',$company);
        }else
        {
            return $this->getErrorResponse('failed to find company with id '.$id);
        }
    }
}
