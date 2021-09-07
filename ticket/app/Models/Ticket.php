<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;


    protected $fillable = 
    [
        'event_id',
        'name',
        'amount',//price of the ticket
        'ordered',
        'status',
        'details_ar',
        'details_en',
        
    ];

    public function event()
    {
        return $this->belongsTo('App\Models\Event');
    }

    public function order()
    {
        return $this->hasMany('App\Models\Order');
    }

    public static function create(array $attr = [])
    {
        $model = new static($attr);
        $model->save();
        return $model;
    }
    
}
