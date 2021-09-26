<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class ResponseController extends Controller
{
    public function getResponseCodes()
    {
        return $this->getSuccessResponse('responseCodes',DB::table('response_codes')->get());
    }
}
