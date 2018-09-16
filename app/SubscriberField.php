<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriberField extends Model
{
    protected $fillable = ['subscriber_id', 'field_id', 'value'];

    /**
     * Establishes a relationship with the Field model
     */
    public function field()
    {
        return $this->belongsTo('App\Field');
    }
}
