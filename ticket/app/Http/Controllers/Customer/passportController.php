<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;

use App\Models\User;

use Validator;
use App\Http\Controllers\Controller;
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
}
