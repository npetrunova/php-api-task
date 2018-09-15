<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $fillable = ['name', 'email', 'state'];
    /**
     * Custom validation messages
     */
    public static $messages = [
        'fields.*.value.required' => 'Value is required for all fields.',
        'fields.*.value.max' => 'Value cannot be bigger than 255 symbols.',
        'fields.*.id.required' => 'ID is required for all fields.',
        'email_domain' => 'Invalid email domain.',
    ];
    /**
     * Validation rules
     */
    public static $rules = [
        'name' => 'required|max:100',
        'email' => 'required|email|email_domain|max:320',
        'fields.*.value' => 'required|max:255',
        'fields.*.id' => 'required'
    ];
    /**
     * Establishing a relationship with SubscriberField model
     */
    public function fields()
    {
        return $this->hasMany('App\SubscriberField');
    }
}
