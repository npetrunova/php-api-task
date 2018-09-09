<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $fillable = ['name', 'email', 'state'];

    public function fields()
    {
        return $this->hasMany('App\SubscriberField');
    }
}
