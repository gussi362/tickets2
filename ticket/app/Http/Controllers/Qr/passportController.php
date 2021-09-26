<?php
/**
 * @group Authentication
 *
 * API endpoints for managing authentication
 */
namespace App\Http\Controllers\Qr;

use Illuminate\Http\Request;

use Validator;
use Hash;

use App\Http\Controllers\Controller;
use App\Models\Company;
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
     * Login user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @respponse {
        "responseCode": 204,
        "responseMessage": "success",
        "responseDescription": "logged in successfully",
        "data": {
            "0": {
                "id": 4,
                "name": "sksks",
                "email": "user2@email.com",
                "phone": null,
                "company_id": 1,
                "type": 2,
                "created_at": "2021-09-04T05:12:51.000000Z",
                "updated_at": "2021-09-13T13:55:24.000000Z",
                "created_by": null
            },
            "token": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiOWZhYWYxYzJkMzViMTM2OTk0NWRjZTViMGZjMDAxYTE0YTUyMDg0YjMxNGM0ZmZjM2EzZjEzNzU5NDc5ZWExZmE0M2FhZTQ5ZTViZTk0NDEiLCJpYXQiOjE2MzIxMjEyMzUuNTY1MDk0LCJuYmYiOjE2MzIxMjEyMzUuNTY1MDk5LCJleHAiOjE2NjM2NTcyMzUuMzkwNjQ2LCJzdWIiOiI0Iiwic2NvcGVzIjpbXX0.FXKhSVxZBAZm0aAaw1g_CIxLoIaZGF8Jyz2m2iCts29Qx8zmell0h3qO0K09Sumfz3vTY8KYI2zPTzNoMJyhz-d7k5RuS2x3PIcas9t7tjEdDzvh2mKFAlsR7atRY_0OzzWCLlhSGxaQzVInk2P6jmt2U5-fWxV5JUDgw719wvB0MyW1-MiDaZrcOoNgjBb7HQJUB1ZNeKBO1nKuN_TtyT-4D1kEJCtKcc41l8PSdtT7Bb4SBykbdurr1L2oFxy-Aw7uzdIjec00lyJS-Fb76gh1xUqQ3JYnnASqabf1pKSgpzphicNobs9cef7AJ5jfW2nltiKWczO4Ey4sihmgNWgTRkHsPtdC-ek0lJ5er8cLetvlfXO3ytZ6-10lJeL03H-TXc2GjIcvUYrdoh7gEb0Gh8IxXhyBqS_OS5Z4VcdBe6V1JswmJ9OY5G2jXXSEHL7b06z_tS9oM4JEh2aWIMis5X_LVftM2a9qObqp5kzMVJq52X_MdkvEVjq4s5hxwnTaB1CzqvTmDyuhzntdMPvNIvkU549U_C8KNB0hdJSZJgkYiL1UVmJXWzJpNXSaUb55sra0-PFTDuXVtf9eg58WIzKhHt_rRecDJzjfPFh4PSFr1EAMSpcZSqhZinGalpDmjJIBuuv7-BgqSbu3OHnafebWowtr7Hsv5pBCqEw"
        }
    }
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

            return $this->getSuccessResponse(trans('messages.passport.loggedin'),['user'=>auth()->user(),'token'=>'Bearer '.$token,'company_name'=>$this->get_company_name()]);
        }else
        {
            return $this->getErrorResponse(trans('messages.passport.invalid_credintals'),'',411);
        }
    }

    private function get_company_name()
    {
        return Company::where('id',auth()->user()->company_id)->first()->name_en;
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
