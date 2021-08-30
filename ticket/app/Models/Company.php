<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = 
    [
        'name_ar',
        'name_en',
        'admin',
        'phone',
        'address',
        'status',
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
        return $this->hasMany('App\Models\Event');
    }

    public function user()
    {
        return $this->hasMany('App\Models\User');
    }
}
