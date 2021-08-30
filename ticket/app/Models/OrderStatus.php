<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;

    protected $fillable=[
        'order_id',
        'type_id'
    ];

    public static function create(array $attr = [])
    {
        $model = new static($attr);
        $model->save();
        return $model;
    }
    
    public function ttype()
    {
        return $this->has('App\Models\ttype');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

}
