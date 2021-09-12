<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\Order;


class DashboardController extends Controller
{
    //TODO: add charts  events:total ,events:tickets
    public function getOverview()
    {
        
        return $this->getSuccessResponse('overview',[
                                        'eventsCount'=>$this->getEventsCount(),
                                        'totalTickets'=>$this->getEventsTotalTickets(),
                                        'totalReservedTickets'=>$this->getEventsTotalReservedTickets(),
                                        'eventsTotal'=>$this->getEventsTotal(),
                                        'lastFiveOrders'=>$this->getLastFiveOrders()
                                        ]);

    } 

    //Overview [number views]
    //use user.company_id 

    private function getEventsCount()
    {
        return Event::where('company_id',auth()->user()->company_id)->get()->count();
    }

    private function getEventsTotal()
    {

        $companyId = auth()->user()->company_id;
        $eventsId = Event::where('company_id',$companyId )->pluck('id');
        return Order::whereIn('event_id', $eventsId)->get()->Sum('amount');
    }

    private function getEventsTotalTickets()
    {
        return Event::where('company_id',auth()->user()->company_id)->get()->Sum('ticket_count');
    }


    private function getEventsTotalReservedTickets()
    {
        $companyId = auth()->user()->company_id;
        $eventsId = Event::where('company_id',$companyId )->pluck('id');
        return Ticket::whereIn('event_id',$eventsId)->get()->Sum('ordered');
    }


    private function getEventsTotalReservedTickets2()
    {//TODO:fix this ,it returns the total of all tickets,tried to do that based on company.event 
        //still it returned event then the tickets total of the event it should be the hole tickets for company.events
        return Event::where('company_id',auth()->user()->company_id)
                            ->with('reservedTickets')
                            ->select('id')
                            ->get();
    }

    private function getLastFiveOrders()
    {
        return Order::orderBy('created_at','desc')->take(5)
                    ->with('ticket',function($query){//we need event_id for order
                        $query->select('event_id','id')
                              ->with('event',function($q){//then we need to get the event name for readable display
                                  $q->select('id','name_en','name_ar');
                                }
                            );
                    })
                    // ->select('id','name','count','amount')
                    ->get();
    }
}
