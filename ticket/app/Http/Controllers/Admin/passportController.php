<?php

namespace App\Http\Controllers\Admin;

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
    
            //return response()->json(['token' => $token], 200);

            $user_data = ['user'=>$user,'token'=>'Bearer '.$token];
            $data = ['responseCode'=>100,
            'responseMessage'=>'Registered successfully',
            'data'=>$user_data];

            return response()->json($data);
    
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

        if ($validator->fails()) {
            $data = ['responseCode'=>102,
                     'responseMessage'=>'not all fields were entered'];
            return response()->json($data);
        }

        $data = [
            'email' => $request -> email,
            'password' => $request -> password
        ];
        //$user = User::where('email','=',$request->email)->first();
        if(auth()->attempt($data))
        {
            
            $token = auth()->user()->createToken('userToken')->accessToken;
           
            $user_data = ['user'=>auth()->user(),'token'=>'Bearer'.$token];

            $data = ['responseCode'=>100,
                     'responseMessage'=>'Logged in successfully',
                     'data'=>$user_data];

            return response()->json($data);
        }else
        {
            
            
            $data = ['responseCode'=>102,
                     'responseMessage'=>'Unauthorized'
                    ];
        }
    }
}
