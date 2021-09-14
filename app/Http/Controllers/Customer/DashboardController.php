<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\Order;
use App\Models\OrderDetails;

class DashboardController extends Controller
{
    // added charts  events:total ,events:tickets ,tickets:checkedIn
    public function getOverview()
    {
        
        return $this->getSuccessResponse('overview',[
                                        'eventsCount'=>$this->getEventsCount(),
                                        'totalTickets'=>$this->getEventsTotalTickets(),
                                        'totalReservedTickets'=>$this->getEventsTotalReservedTickets(),
                                        'eventsTotal'=>$this->getEventsTotal(),
                                        'lastFiveOrders'=>$this->getLastFiveOrders(),
                                        
                                        'charts'               => ['events' => $this->getEventsCount(),
                                                                    'reservedTickets' => $this->getEventsTotalReservedTickets(),
                                                                    'ticketsCheckedIn' => $this->getCheckedInCount()
                                                                ]
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
        $eventsId = Event::where('company_id',auth()->user()->company_id )->pluck('id');
        return Ticket::whereIn('event_id',$eventsId)->get()->Sum('ordered');
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

    //charts info
    private function getCheckedInCount()
        {        
            //this user company ,this user eevents ,this user orders ,this users checkedIn tickets
            $companyId = auth()->user()->company_id;
            $events = Event::where('company_id',$companyId)->pluck('id');
            $orders = Order::whereIn('event_id',$events)->pluck('code');
            return $orderDetails = OrderDetails::where(function ($query) use ($orders)
                                                        {
                                                            foreach($orders as $code)
                                                            {
                                                                $query->orWhere('serial','like','%'.$code.'%');
                                                            }
                                                        })->count();
            
        }
}