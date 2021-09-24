<?php

namespace App\Http\Controllers\Admin;

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
         $users = User::orderBy('id')->get();
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
            'phone' => 'required',
            'type'  => 'required'
        ]);

        if ($validator->fails()) 
        {
            return $this->getErrorResponse(trans('messages.errors.input_data'),$validator->errors(),410);
        }
        
        $data =[];
        if($request->company_id)
        {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'company_id' => $request->company_id,
                'phone' => $request->phone,
                'type' => $request->type,
                'created_by' => auth()->user()->id
            ];
        }else
        {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone' => $request->phone,
                'type' => $request->type,
                'created_by' => auth()->user()->id
            ];
        }
        $user = User::create($data);
        
        if($user->exists())
        {
            return $this->getSuccessResponse(trans('messages.generic.successfully_added_new' ,['new' => trans('messages.model.user')]),$user );
        }else
        {
            return $this->getErrorResponse(trans('messages.errors.system_error') ,'',501);
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
        
        foreach ($request->all() as $key => $value) {
            //if ($value->$key) {
            if ($value) {
                $user->$key = $value;
            }

        }

        if($user->update())
        {
            return $this->getSuccessResponse(trans('messages.generic.successfully_updated' ,['new' => trans('messages.model.user')]),$user );
        }else
        {
            return $this->getErrorResponse(trans('messages.errors.system_error'), '' ,502);
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
        if($user->delete())
        {
            return $this->getSuccessResponse(trans('messages.generic.successfully_deleted' ,['new' => trans('messages.model.user')]),$user );
        }else
        {
            return $this->getErrorResponse(trans('messages.errors.system_error'),'',503);
        }
    }
}
