<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\Order;
use App\Models\Event;
use DB;
class OrderAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orders;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($order ,$sendsms = false)
    {
        $this->orders = Order::orderBy('created_at','desc')->get();
        if($sendsms)
        {
            $this->sendSMSnotifications($order);    
        }
        
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('orderChannel');
    }

    /**
     * send sms to order.phone
     * @param App\Models\Order order
     */
    private function sendSMSnotifications($order)
    {
        //TODO::add sms lib to handle it ,meanwhile just to prove it works store in db dump table
        $sms_en = "order for".$this->getEventName($order->event_id)." completed ,\n"
                    ."name : $order->name ,\n number of tickets :".$order->count." ,\n"
                    ."Price : $order->amount.";
        
                    DB::table('sms_dump')->insert(['sms' => $sms_en ]);
    }

    private function getEventName($event_id)
    {
        return Event::where('id',$event_id)->first()->name_en;
    }

    //TODO::FIX ORDER COUNT BASED ON TICKETS.COUNT
    // private function getTicketsCount($ticket)
    // {
    //     $tickets = json_decode($ticket);
    //     $count = 0;
    //     for($i=0 ;$i<count($ticket); $i+=1)
    //     {
    //         $count += $tickets[$i]->count;
    //     }

    //     return $count;
    // }
}
