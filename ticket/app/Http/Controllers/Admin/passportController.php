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

            return $this->getSuccessResponse(trans('messages.passport.registered') ,[$user,'token'=>'Bearer '.$token]);
    
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
            return $this->getErrorResponse(trans('messages.errors.input_data'),$validator->errors(),'',410);
        }

        $data = [
            'email' => $request -> email,
            'password' => $request -> password
        ];
        //$user = User::where('email','=',$request->email)->first();
        if(auth()->attempt($data))
        {
            $token = auth()->user()->createToken('userToken')->accessToken;

            return $this->getSuccessResponse(trans('messages.passport.loggedin'),['user'=>auth()->user(),'token'=>'Bearer '.$token]);
        }else
        {
            return $this->getErrorResponse(trans('messages.passport.invalid_credintals'),'',411);
        }
    }

    public function changePassword(Request $request)
    {
        // return \Lang::get('messages.date.last_week');
        $validator =  Validator::make($request->all(),[
            'old_password' => 'required|string',
            'new_password' => 'required|string',
            'new_password_confirmation' => 'required|string|same:new_password'
        ]);

        if ($validator->fails()) 
        {
            return $this->getErrorResponse(trans('messages.errors.input_data'),$validator->errors(),'',410);
        }

        $user = User::where('id',auth()->user()->id)->first();

        //if(bcrypt($request->old_password == bcrypt($request->new_password)) it's impossible to compare hashed password ,cause the salt everytime a password is hashed is different
        if ( !Hash::check($request->old_password ,$user->password) ) 
        {
            return $this->getErrorResponse(trans('messages.passport.wrong_old_password'),'',415);
        }

        if( Hash::check($request->new_password ,$user->password) )
        {
            return $this->getErrorResponse(trans('messages.passport.passwords_match'),'',414);
        }

        $user->password = bcrypt($request->new_password);

        if($user->update())
        {
            return $this->getSuccessResponse(trans('messages.passport.changed_password') ,$user);
        }else
        {
            return $this->getErrorResponse(trans('messages.passport.failed_change_password'),'',510);//send system error
        }
    }

}
