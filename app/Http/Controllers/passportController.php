<?php

/**
 * @group Authentication
 *
 * API endpoints for managing authentication
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

use Validator;
use Hash;
use App\Http\Controllers\Controller;
use App\Models\Company;
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
     * @respponse {
     *   "responseCode": 204,
     *   "responseMessage": "success",
     *   "responseDescription": "logged in successfully",
     *   "data": {
     *       "0": {
     *           "id": 4,
     *           "name": "sksks",
     *           "email": "user2@email.com",
     *           "phone": null,
     *           "company_id": 1,
     *           "type": 2,
     *           "created_at": "2021-09-04T05:12:51.000000Z",
     *           "updated_at": "2021-09-13T13:55:24.000000Z",
     *           "created_by": null
     *       },
     *       "token": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiOWZhYWYxYzJkMzViMTM2OTk0NWRjZTViMGZjMDAxYTE0YTUyMDg0YjMxNGM0ZmZjM2EzZjEzNzU5NDc5ZWExZmE0M2FhZTQ5ZTViZTk0NDEiLCJpYXQiOjE2MzIxMjEyMzUuNTY1MDk0LCJuYmYiOjE2MzIxMjEyMzUuNTY1MDk5LCJleHAiOjE2NjM2NTcyMzUuMzkwNjQ2LCJzdWIiOiI0Iiwic2NvcGVzIjpbXX0.FXKhSVxZBAZm0aAaw1g_CIxLoIaZGF8Jyz2m2iCts29Qx8zmell0h3qO0K09Sumfz3vTY8KYI2zPTzNoMJyhz-d7k5RuS2x3PIcas9t7tjEdDzvh2mKFAlsR7atRY_0OzzWCLlhSGxaQzVInk2P6jmt2U5-fWxV5JUDgw719wvB0MyW1-MiDaZrcOoNgjBb7HQJUB1ZNeKBO1nKuN_TtyT-4D1kEJCtKcc41l8PSdtT7Bb4SBykbdurr1L2oFxy-Aw7uzdIjec00lyJS-Fb76gh1xUqQ3JYnnASqabf1pKSgpzphicNobs9cef7AJ5jfW2nltiKWczO4Ey4sihmgNWgTRkHsPtdC-ek0lJ5er8cLetvlfXO3ytZ6-10lJeL03H-TXc2GjIcvUYrdoh7gEb0Gh8IxXhyBqS_OS5Z4VcdBe6V1JswmJ9OY5G2jXXSEHL7b06z_tS9oM4JEh2aWIMis5X_LVftM2a9qObqp5kzMVJq52X_MdkvEVjq4s5hxwnTaB1CzqvTmDyuhzntdMPvNIvkU549U_C8KNB0hdJSZJgkYiL1UVmJXWzJpNXSaUb55sra0-PFTDuXVtf9eg58WIzKhHt_rRecDJzjfPFh4PSFr1EAMSpcZSqhZinGalpDmjJIBuuv7-BgqSbu3OHnafebWowtr7Hsv5pBCqEw"
     *   }
    *}
     *  Login usexr
     * @group authpoint
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * 
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

            return $this->getSuccessResponse(trans('messages.passport.loggedin'),[auth()->user(),'token'=>'Bearer '.$token ,'company_name'=>$this->get_company_name(),'company_id'=>auth()->user()->company_id]);
        }else
        {
            return $this->getErrorResponse(trans('messages.passport.invalid_credintals'),'',411);
        }
    }

    private function get_company_name()
    {
        return Company::where('id',auth()->user()->company_id)->first()->name_en;
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

            return $this->getSuccessResponse(trans('messages.passport.send_sms_rest_code',['phone' =>$user->phone]),$user);
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
                return $this->getSuccessResponse(trans('messages.passport.changed_password') ,$user);
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
