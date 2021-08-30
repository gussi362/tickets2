<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Date extends Model
{
    use HasFactory;


    protected $fillable = 
    [
        'event_id',
        'date',
        'created_by'
    ];

    public static function create(array $attr = [])
    {
        $model = new static($attr);
        $model->save();
        return $model;
    }
    
    public function event()
    {
        return $this->belongsTo('App\Models\Event');
    }
}
