<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'id',
        'serial',
        'price',
        'ticket_id',
        'status'
    ];

    public static function create(array $attr = [])
    {
        $model = new static($attr);
        $model->save();
        return $model;
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
    
}
