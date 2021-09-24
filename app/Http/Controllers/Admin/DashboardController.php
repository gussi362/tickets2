<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use App\Models\Order;

use App\Models\Ticket;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OrderDetails;

class DashboardController extends Controller
{
    public function getOverview()
    {

        return $this->getSuccessResponse(trans('messages.models.overview'),[
                                        'companiesCount'       => $this->getCompaniesCount(),
                                        'eventsCount'          => $this->getEventsCount(),
                                        'totalTickets'         => $this->getEventsTotalTickets(),
                                        'totalReservedTickets' => $this->getEventsTotalReservedTickets(),
                                        'eventsTotal'          => $this->getEventsTotal(),
                                        'lastFiveEvents'       => $this->getLastFiveEvents(),
                                        
                                        'charts'               => ['events' => $this->getEventsCount(),
                                                                    'reservedTickets' => $this->getEventsTotalReservedTickets(),
                                                                    'ticketsCheckedIn' => $this->getCheckedInCount()
                                                                    ]
                                        ]);

    } 

    //Overview [number views]
    private function getCompaniesCount()
    {
        return Company::get()->count();
    }

    private function getEventsCount()
    {
        return Event::get()->count();
    }

    private function getEventsTotal()
    {
        return Order::get()->Sum('amount');
    }

    private function getEventsTotalTickets()
    {
        return Event::get()->Sum('ticket_count');
    }

    private function getEventsTotalReservedTickets()
    {
        return Ticket::get()->Sum('ordered');
    }

    private function getLastFiveEvents()
    {//TODO:How to turn all of these into relationship .eg ordersTotal for event from event model 
        //latest not working with realtionship
        return Event::orderBy('created_at','desc')
                    ->take(5)
                    ->with('reservedTickets')
                    ->with('companyName')
                    ->with('eventTotal')
                    ->select('name_en','name_ar','ticket_count','company_id','id')
                    ->get();
    }

//charts info
    private function getCheckedInCount()
        {        
            return OrderDetails::where('status','true')->count();
        }
}
