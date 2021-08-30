<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponser extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'name_ar',
        'name_en',
        'image',
        'event_id'
    ];


    public static function create(array $attr = [])
    {
        $model = new static($attr);
        $model->save();
        return $model;
    }
    
    public function event()
    {
        return $this->bleongsTo('App\Models\Event');
    }
}
