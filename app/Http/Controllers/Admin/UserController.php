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
         return $this->getSuccessResponse('retrieved users successfully' ,$users);

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
            return $this->getErrorResponse('not all fields were entered');
        }
        
        $data =[];
        if($request->company_id)
        {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'company_id' => $request->company_id,
                'type' => $request->type,
                'created_by' => auth()->user()->id
            ];
        }else
        {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'type' => $request->type,
                'created_by' => auth()->user()->id
            ];
        }
        $user = User::create($data);
        
        if($user->exists())
        {
            return $this->getSuccessResponse('created user successfully',$user);
        }else
        {
            return $this->getErrorResposne('failed to create user');
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
        
        return $this->getSuccessResponse('user found',$user);
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
            return $this->getSuccessResponse('updated user successfully',$user);
        }else
        {
            return $this->getErrorResponse('failed to update user with id '.$id);
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
            return $this->getSuccessResponse('deleted user with id '.$id,$user);
        }else
        {
            return $this->getErrorResposne('failed to delete user with id '.$id);
        }
    }
}
