<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriberField extends Model
{
    protected $fillable = ['subscriber_id', 'field_id', 'value'];

    public function field()
    {
        return $this->belongsTo('App\Field');
    }
}
