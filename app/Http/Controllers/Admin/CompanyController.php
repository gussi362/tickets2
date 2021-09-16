<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Company;
use Validator;
use App\Http\Controllers\Controller;

use App\Events\Dashboard\Admin\overviewChanged;
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
        return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.model.company')]),$company);
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
        ]);

        if ($validator->fails()) 
        {
            return $this->getErrorResponse(trans('messages.errors.input_data'),$validator->errors());
        }
        try{
            $data = $request->all();
            $data['created_by'] = auth()->user()->id;
            $company = Company::create($data);
            if($company->exists())
            {
                broadcast(new overviewChanged($company));
                return $this->getSuccessResponse(trans('messages.generic.successfully_added_new' ,['new' => trans('messages.models.company')]),$company);
            }else
            {           
                return $this->getErrorResponse(trans('messages.error.system_error'));
            }
        }catch(\Exception $e)
        {
            return $this->getErrorResponse(trans('messages.error.system_error'));
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
        return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.models.company')]),$company);
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
            return $this->getSuccessResponse(trans('messages.generic.successfully_updated' ,['new' => trans('messages.model.company')]),$company);
        }else
        {
            return $this->getErrorResponse(trans('messages.errors.system_error').$id);
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
            return $this->getSuccessResponse(trans('messages.generic.successfully_deleted' ,['new' => trans('messages.model.company')]),$company);
        }else
        {
            return $this->getErrorResponse(trans('messages.error.system_error'));
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

        return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.model.event')]),$companies);
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
            return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.model.event')]),$company);
        }else
        {
            return $this->getErrorResponse(trans('messages.error.system_error'));
        }
    }
}
