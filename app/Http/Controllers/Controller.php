<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * standard return data to client on sucess
     * @param code default 100=sucess
     * @param message 
     * @param data to return
     */
    public function getSuccessResponse($message ,$data ,$code = 100 )
    {
        return response()->json(
            [
                'responseCode'=>$code,
                'responseMessage'=>'success',
                'responseDescription'=>$message,
                'data'=>$data
            ]);
    }

     /**
     * standard return data to client on failure
     * @param code default 102=error
     * @param message 
     * @param data to return
     */
    public function getErrorResponse($message ,$data ="",$code = 102 )
    {
        return response()->json(
            [
                'responseCode'=>$code,
                'responseMessage'=>'fail',
                'responseDescription'=>$message,
                'data'=>$data
            ]);
    }
}
