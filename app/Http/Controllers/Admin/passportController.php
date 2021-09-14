<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Validator;
use Hash;

use App\Http\Controllers\Controller;
use App\Models\User;
class passportController extends Controller
{
    

           
    /**
     * Register user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function register(Request $request)
    {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'company_id' => $request->company_id,
                'type' => $request->type
                ]);
     
            $token = $user->createToken('userToken')->accessToken;

            return $this->getSuccessResponse('Registered successfully' ,[$user,'token'=>'Bearer '.$token]);
    
    }
    
    
    /**
     * Login
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        $validator =  Validator::make($request->all(),[
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) 
        {
            return $this->getErrorResponse('not all fields were entered');
        }

        $data = [
            'email' => $request -> email,
            'password' => $request -> password
        ];
        //$user = User::where('email','=',$request->email)->first();
        if(auth()->attempt($data))
        {
            $token = auth()->user()->createToken('userToken')->accessToken;

            return $this->getSuccessResponse('Logged in successfully' ,[auth()->user(),'token'=>'Bearer '.$token]);
        }else
        {
            return $this->getErrorResponse('invalid username or password.');
        }
    }

    public function changePassword(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'old_password' => 'required|string',
            'new_password' => 'required|string',
            'new_password_confirmation' => 'required|string'
        ]);

        if ($validator->fails()) 
        {
            return $this->getErrorResponse('not all fields were entered');
        }

        $user = User::where('id',auth()->user()->id)->first();

        //if(bcrypt($request->old_password == bcrypt($request->new_password)) it's impossible to compared hashed password ,cause the salt everytime a password is hashed is different
        if ( !Hash::check($request->old_password ,$user->password) ) 
        {
            return $this->getErrorResponse('wrong old password');
        }

        if( Hash::check($request->new_password ,$user->password) )
        {
            return $this->getErrorResponse('can\'t use the same password');
        }

        if( $request->new_password != $request->new_password_confirmation )
        {
            return $this->getErrorResponse('new password and new password confirmation doesn\'t match');
        }

        $user->password = bcrypt($request->new_password);

        if($user->update())
        {
            return $this->getSuccessResponse('changed password successfully' ,$user);
        }else
        {
            return $this->getErrorResponse('failed to change password');
        }
    }

}
