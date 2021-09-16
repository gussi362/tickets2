<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Models\User;

use Validator;
use App\Http\Controllers\Controller;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $users = User::where('company_id',auth()->user()->company_id)
                        ->orderBy('id')->get();
        return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.model.user')]),$users);

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
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'type'  => 'required'
        ]);

        if ($validator->fails()) 
        {
            return $this->getErrorResponse(trans('messages.errors.input_data'),$validator->errors());
        }
        
        if(!in_array($request->type,[2,3]))
        {
            return $this->getErrorResponse(trans('messages.errors.unauthorized_opreation'));
        }

        //all user created by customer are automatically under the same company as the account that created them
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'company_id' => auth()->user()->company_id,
                'type' => $request->type,
                'created_by' => auth()->user()->id
            ];
        $user = User::create($data);
        
        if($user->exists())
        {
            return $this->getSuccessResponse(trans('messages.generic.successfully_added_new' ,['new' => trans('messages.model.user')]),$user);
        }else
        {
            return $this->getErrorResponse(trans('messages.errors.system_error'));
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
        $user = User::findorfail($id);
        
        if($user->company_id != auth()->user()->company_id )
        {
            return $this->getErrorResponse(trans('messages.errors.unauthorized_opreation'));
        }

        return $this->getSuccessResponse(trans('messages.generic.successfully_found' ,['new' => trans('messages.model.user')]),$user);
    }

    /**
     * Upuser the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user= User::findorfail($id);
        //can't edit or delted the account that created you 
        
        //if !  from the same company && ! created by auth()->user()
        //can only update if from own company
        //can update only created by him
        
        if ( ($user->company_id != auth()->user()->company_id ) || ($user->created_by != auth()->user()->id) ) 
        {
            return $this->getErrorResponse(trans('messages.errors.unauthorized_opreation'));
        }

        foreach ($request->all() as $key => $value) {
            //if ($value->$key) {
            if ($value) 
            {
                $user->$key = $value;
            }

        }

        if($user->update())
        {
            return $this->getSuccessResponse(trans('messages.generic.successfully_updated' ,['new' => trans('messages.model.user')]),$user);
        }else
        {
            return $this->getErrorResponse(trans('messages.errors.system_error'));
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
        $user = User::findorFail($id);
        //can't edit or delted the account that created you 
        if ( ($user->company_id != auth()->user()->company_id ) || ($user->created_by != auth()->user()->id) ) 
        {
            return $this->getErrorResponse(trans('messages.errors.unauthorized_opreation'));
        }

        if($user->delete())
        {
            return $this->getSuccessResponse(trans('messages.generic.successfully_deleted' ,['new' => trans('messages.model.user')]),$user);
        }else
        {
            return $this->getErrorResponse(trans('messages.errors.system_error'));
        }
    }
}
