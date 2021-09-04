<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Route::get('eventsz','Admin\EventController@getCurrentEvents');
// passport routes 
Route::post('login', 'passportController@login');

Route::post('register', 'passportController@register');



Route::group(['prefix' => 'admin','middleware'=>['checkStatus','auth:api'], 'namespace'=>'Admin'], function () {

    Route::apiResource('company','CompanyController');
    
    Route::apiResource('event','EventController');
    
    Route::apiResource('order','OrderController');

    Route::apiResource('orderDetails','OrderDetailsController');    
    Route::apiResource('ticket','TicketController');
    
    Route::apiResource('date','DateController');

    Route::apiResource('sponser','SponserController');

Route::group(['prefix' => 'dashboard'], function(){

    Route::get('/','CompanyController@getCompaniesWithEvents');
    Route::get('/{company_id}','CompanyController@getCompanyWithEvents');

    Route::get('event/current','EventController@getEventsCurrent');
    Route::get('event/{event_id}','EventController@getCompanyEvent');
    Route::get('event/company/{company_id}','EventController@getCompanyEvents');
    
});
    
});

//user routes
Route::group(['prefix' => 'user','middleware'=>['checkStatusUser','auth:api'],'namespace' => 'Customer'], function () {
    Route::apiResource('event','EventController');
    
    Route::apiResource('order','OrderController');

    Route::apiResource('orderDetails','OrderDetailsController');
    
    Route::apiResource('ticket','TicketController');
    
    Route::apiResource('date','DateController');

    Route::apiResource('sponser','SponserController');

    Route::group(['prefix' => 'dashboard'], function(){

        Route::get('event/current','EventController@getEventsCurrent');
        Route::get('event/{event_id}','EventController@getCompanyEvent');
        Route::get('event/company/{company_id}','EventController@getCompanyEvents');
    
        //dashboard 
    
        Route::get('events','EventController@getEventsDetails');
        Route::get('events/details','EventController@getCompanyCurrentEventsDetails');
        Route::get('tickets','TicketController@getCompanyTicketsDetails');
    
    });

    
});


//user who scan ids //add middleware later
Route::group(['prefix' => 'scan','middleware'=>['checkStatusUser','auth:api'] ,'namespace' => 'Qr'], function () {

    //Route::get('events','EventController@getCompanyCurrentEventsDetails');
    Route::get('orderStatus/{order_id}/{serial}','OrderController@checkIn');
    Route::apiResource('order','OrderController');
    });


//payments
Route::group(['prefix' => 'appname' ,'namespace' => 'Api'], function () {

    Route::get('events','EventController@getEventsCurrent');

    Route::get('/{order_id}','PaymentController@getOrder');

    Route::post('pay/{order_id}','PaymentController@pay');
    //events
    
});

    //Route::get('events','EventController@getCurrentEvents');
    


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });->only(['getCurrentEvents']);
