<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = 
    [
        'name_ar',
        'name_en',
        'details_ar',
        'details_en',
        'first_date',
        'last_date',
        'ticket_count',
        'image',
        'status',
        'company_id',
        'coordinates'
    ];

    public static function create(array $attr = [])
    {
        $model = new static($attr);
        $model->save();
        return $model;
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function companyName()
    {
        return $this->belongsTo('App\Models\Company','company_id', 'id')->select('id', 'name_ar', 'name_en');
    }

    public function date()
    {
        return $this->hasMany('App\Models\Date');
    }

    public function ticket()
    {
        return $this->hasMany('App\Models\Ticket');
    }

    public function reservedTickets()
    {
        return $this->hasMany('App\Models\Ticket','event_id','id')->select('ordered','event_id');
    }

    public function eventTotal()
    {
        return $this->hasMany('App\Models\Order')->Sum('amount');
    }

    //get orderedTicketsCounts and total
    public function ticketCount()
    {
        //how to add foreign key
        //return $this->hasManyThrough('App\Models\Ticket','App\Models\Order');
        return $this->hasMany('App\Models\Ticket')->withCount('order')->withSum('order','amount');

    }

    public function attendedTickets()
    {
        return $this->hasMany('App\Models\Ticket')->withCount(['order' => function($query)
        {
            $query->where('type_id',4)->get();
        }]);
    }

    public function user()
    {
        return $this->bleongsTo('App\Models\User');
    }

    public function sponser()
    {
        return $this->hasMany('App\Models\Sponser');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

    // public function availableEvents() {
    //     return $this->where('status','=', 'true')->where('date','>=','current_date');
    // }
}
