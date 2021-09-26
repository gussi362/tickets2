<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ttype extends Model
{
    use HasFactory;

    public $timestamps = false;//this model doesn't have timestamps in db 
    protected $fillable = 
    [
        'id',
        'name_en',
        'name_ar',
        'set'
    ];
    
    public function order_status()
    {
        return $this->belongsTo('App\Models\OrderStatus');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
}
