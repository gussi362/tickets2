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

            return $this->getSuccessResponse(trans('messages.passport.registered') ,[$user,'token'=>'Bearer '.$token],207);
    
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
            return $this->getErrorResponse(trans('messages.errors.input_data'),$validator->errors(),410);
        }

        $data = [
            'email' => $request -> email,
            'password' => $request -> password
        ];
        //$user = User::where('email','=',$request->email)->first();
        if(auth()->attempt($data))
        {
            $token = auth()->user()->createToken('userToken')->accessToken;

            return $this->getSuccessResponse(trans('messages.passport.loggedin'),[auth()->user(),'token'=>'Bearer '.$token],204);
        }else
        {
            return $this->getErrorResponse(trans('messages.passport.invalid_credintals'),'',411);
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
            return $this->getErrorResponse(trans('messages.errors.input_data'),$validator->errors(),'',410);
        }

        //check email 
        $user = User::where('email',$request->email);

        if($user->doesntExist())
        {
            return $this->getErrorResponse(trans('messages.passport.email_doesnt_exists'),'',412);
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

            return $this->getSuccessResponse(trans('messages.passport.send_sms_rest_code',['phone' =>$user->phone]),$user,220);
        } catch (\Exception $e) 
        {
            return $this->getErrorResponse(trans('messages.errors.system_error') ,$e->getMessage(),510);
        }
        
        //
    }

    //real reset here 
    public function resetPasswordWithToken(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'token'  => 'required',//phone add 
            'password' => 'required|string',
            'password_confirmation' => 'required|string|same:password'
        ]);
        
        if ($validator->fails()) 
        {
            return $this->getErrorResponse(trans('messages.errors.input_data'),$validator->errors(),'',410);
        }

        try 
        {
            //check if token exists
            if( !$password_rest = \DB::table('password_resets')->where('token',$request->token)->first() )
            {
                return $this->getErrorResponse(trans('messages.passport.invalid_reset_code'),'',413);
            }  

            //then get user
            if ( !$user = User::where('phone',$password_rest->phone)->first() ) 
            {
                return $this->getErrorResponse(trans('messages.passport.email_doesnt_exists'),'',412);
            }

            //then check if new pass = old pass
            if( Hash::check($request->password ,$user->password) )
            {
                return $this->getErrorResponse(trans('messages.passport.passwords_match'),'',313);
            }
            
            $user->password = bcrypt($request->password);
            
            //clean up all tokens of $user.phone 
            \DB::table('password_resets')->where('phone',$user->phone)->delete();
            if($user->update())
            {
                return $this->getSuccessResponse(trans('messages.passport.changed_password') ,$user,205);
            }else
            {
                return $this->getErrorResponse(trans('messages.errors.system_error'),'',510);
            }
        } catch (\Exception $e) 
        {
            return $this->getErrorResponse(trans('messages.errors.system_error') ,$e->getMessage(),'',510);
        } 


    }


    //genereate 6 digts number for password resets code
    private function generateCode()
    {
        $a ='';
        for ($i = 0; $i<6; $i++) 
        {
            $a .= rand(0000000,999999);
        }

        return $a;
    }
}
