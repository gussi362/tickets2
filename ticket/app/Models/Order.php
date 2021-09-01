<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $fillable = 
    [
        'id',
        'ticket_id',
        'date_id',
        'name',
        'phone',
        'count',//number of requesprotected $casts = ['id' => 'string'];ted tickets for this order
        'amount',//the total of the order (ticket_price * order_count)
        'status',
        'payment',
        'code'
    ];

    protected $casts = [
        'id' => 'string'
    ];
    
    public static function create(array $attr = [])
    {
        $model = new static($attr);
        $model->save();
        return $model;
    }
    
    public function ticket()
    {
        return $this->belongsTo('App\Models\Ticket');
    }

    public function events()
    {
        return $this->belongsTo('App\Models\Events');
    }
    
    public function date()
    {
        return $this->belongsTo('App\Models\Date');
    }

    public function orderDetails()
    {
        return $this->hasMany('App\Models\OrderDetails');
    }

    public function orderStatus()
    {
        return $this->hasMany('App\Models\OrderStatus');
    }

    public function ttype()
    {
        return $this->has('App\Models\ttype');
    }

}
