<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Company;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Order;

class DashboardController extends Controller
{
    public function getOverview()
    {

        return $this->getSuccessResponse('overview',[
                                        'companiesCount'=>$this->getCompaniesCount(),
                                        'eventsCount'=>$this->getEventsCount(),
                                        'totalTickets'=>$this->getEventsTotalTickets(),
                                        'totalReservedTickets'=>$this->getEventsTotalReservedTickets(),
                                        'eventsTotal'=>$this->getEventsTotal(),
                                        'lastFiveEvents'=>$this->getLastFiveEvents()
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
        return Event::take(5)
                    ->with('reservedTickets')
                    ->with('companyName')
                    ->with('eventTotal')
                    ->select('name_en','name_ar','ticket_count','company_id','id')
                    ->get();
    }
}
