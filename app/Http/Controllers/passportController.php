<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

use Validator;
use Hash;
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


    public function resetPassword(Request $request)
    {
           
        #enter phone no or email and check if it exists
        #if yes send random.code to phone
        
        $validator =  Validator::make($request->all(),[
            'email' => 'required|string',
        ]);

        if ($validator->fails()) 
        {
            return $this->getErrorResponse('not all fields were entered');
        }

        //check email 
        $user = User::where('email',$request->email);

        if($user->doesntExist())
        {
            return $this->getErrorResponse('this email doesn\'t exists');
        }

        $user = $user->first();
        
        //token is the 6 digts code we send to user.phone to verify himself 
        $token = $this->generateCode();

        //then save phone||email % $token in password resets table
        try 
        {
            
            \DB::table('password_resets')->insert(
                [
                   'phone' => $user->phone,
                   'token' => $token 
                ]);            

            //send sms here

            return $this->getSuccessResponse('check your phone ...'.substr($user->phone,-4).' for reset code.',$user);
        } catch (\Exception $e) 
        {
            return $this->getErrorResponse('exception error' ,$e->getMessage());
        }
        
        //
    }

    //real reset here 
    public function resetPasswordWithToken(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'token'  => 'required',
            'password' => 'required|string',
            'password_confirmation' => 'required|string'
        ]);
        
        if ($validator->fails()) 
        {
            return $this->getErrorResponse('not all fields were entered');
        }

        try 
        {
            //check if token exists
            if( !$password_rest = \DB::table('password_resets')->where('token',$request->token)->first() )
            {
                return $this->getErrorResponse('invalid reset code');
            }  

            //then get user
            if ( !$user = User::where('phone',$password_rest->phone)->first() ) 
            {
                return $this->getErrorResponse('user doesn\'t exists');
            }

            //then check if new pass = old pass
            if( Hash::check($request->password ,$user->password) )
            {
                return $this->getErrorResponse('can\'t use the same password');
            }

            if( $request->password != $request->password_confirmation )
            {
                return $this->getErrorResponse('new password and new password confirmation doesn\'t match');
            }
            
            $user->password = bcrypt($request->password);
            
            //clean up all tokens of $user.phone 
            \DB::table('password_resets')->where('phone',$user->phone)->delete();
            if($user->update())
            {
                return $this->getSuccessResponse('changed password successfully' ,$user);
            }else
            {
                return $this->getErrorResponse('failed to change password');
            }
        } catch (\Exception $e) 
        {
            return $this->getErrorResponse('failed to create order' ,$e->getMessage());
        } 


    }


    //genereate 6 digts number for password resets code
    private function generateCode()
    {
        $a ='';
        for ($i = 0; $i<6; $i++) 
        {
            $a .= mt_rand(0,9);
        }

        return $a;
    }
}
