<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriberField extends Model
{
    protected $fillable = ['subscriber_id', 'field_id', 'value'];
    /**
     * Custom validation messages
     */
    public static $messages = [
        'fields.*.value.required' => 'Value is required for all fields.',
        'fields.*.value.max' => 'Value cannot be bigger than 255 symbols.',
        'fields.*.id.required'    => 'ID is required for all fields.',
    ];
     /**
     * Validation rules
     */
    public static $rules = [
        'fields.*.value' => 'required|max:255',
        'fields.*.id' => 'required'
    ];
    /**
     * Establishes a relationship with the Field model
     */
    public function field()
    {
        return $this->belongsTo('App\Field');
    }
}
