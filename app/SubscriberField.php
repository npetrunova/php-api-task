<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriberField extends Model
{
    protected $fillable = ['subscriber_id', 'field_id', 'value'];

    public static $messages = [
        'fields.*.value.required' => 'Value is required for all fields.',
        'fields.*.value.max' => 'Value cannot be bigger than 255 symbols.',
        'fields.*.id.required'    => 'ID is required for all fields.',
    ];


    public static $rules = [
        'fields.*.value' => 'required|max:255',
        'fields.*.id' => 'required'
    ];

    public function field()
    {
        return $this->belongsTo('App\Field');
    }
}
