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



Route::group(['prefix' => 'admin','middleware'=>['checkStatus','auth:api']], function () {

    Route::apiResource('company','Admin\CompanyController');
    Route::get('/dashboard','Admin\CompanyController@getCompaniesWithEvents');
    Route::get('/dashboard/{company_id}','Admin\CompanyController@getCompanyWithEvents');

    Route::apiResource('event','Admin\EventController');
    
    Route::apiResource('order','Admin\OrderController');

    Route::apiResource('orderDetails','Admin\OrderDetailsController');
    Route::apiResource('orderStatus','Admin\OrderStatusController');
    
    Route::apiResource('ticket','Admin\TicketController');
    
    Route::apiResource('date','Admin\DateController');

    Route::apiResource('sponser','Admin\SponserController');

    //Route::get('/dashboard/allevent','Admin\EventController@getEventsCurrent');
    Route::get('/dashboard/event/current','Admin\EventController@getEventsCurrent');
    Route::get('/dashboard/event/{event_id}','Admin\EventController@getCompanyEvent');
    Route::get('/dashboard/event/company/{company_id}','Admin\EventController@getCompanyEvents');
    //Route::get('/dashboard/events/details','Admin\EventController@getCompanyCurrentEventsDetails');
    //Route::get('/dashboard/tickets','Admin\TicketController@getCompanyTicketsDetails');

    
});

//user routes
Route::group(['prefix' => 'user','middleware'=>['checkStatusUser','auth:api']], function () {
    Route::apiResource('event','Customer\EventController');
    
    Route::apiResource('order','Customer\OrderController');

    Route::apiResource('orderDetails','Customer\OrderDetailsController');
    
    Route::apiResource('ticket','Customer\TicketController');
    
    Route::apiResource('date','Customer\DateController');

    Route::apiResource('sponser','Customer\SponserController');


    Route::get('/dashboard/event/current','Customer\EventController@getEventsCurrent');
    Route::get('/dashboard/event/{event_id}','Customer\EventController@getCompanyEvent');
    Route::get('/dashboard/event/company/{company_id}','Customer\EventController@getCompanyEvents');

    //dashboard 

    Route::get('/dashboard/events','Customer\EventController@getEventsDetails');
    Route::get('/dashboard/events/details','Customer\EventController@getCompanyCurrentEventsDetails');
    Route::get('/dashboard/tickets','Customer\TicketController@getCompanyTicketsDetails');

    
});


//user who scan ids //add middleware later
Route::group(['prefix' => 'scan','middleware'=>['checkStatusUser','auth:api']], function () {

    //Route::get('events','EventController@getCompanyCurrentEventsDetails');
    Route::get('orderStatus/{order_id}/{serial}','Qr\OrderController@checkIn');
    Route::apiResource('order','Qr\OrderController');
    });


//payments
Route::group(['prefix' => 'appname'], function () {

    Route::get('events','Api\EventController@getEventsCurrent');

    Route::get('/{order_id}','Api\PaymentController@getOrder');

    Route::post('pay/{order_id}','Api\PaymentController@pay');
    //events
    
});

    //Route::get('events','EventController@getCurrentEvents');
    


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });->only(['getCurrentEvents']);
