<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $fillable = ['name', 'email', 'state'];

    public static $messages = [
        'fields.*.value.required' => 'Value is required for all fields.',
        'fields.*.value.max' => 'Value cannot be bigger than 255 symbols.',
        'fields.*.id.required'    => 'ID is required for all fields.',
    ];

    public static $rules = [
        'name' => 'required|max:100',
        'email' => 'required|email|max:320',
        'fields.*.value' => 'required|max:255',
        'fields.*.id' => 'required'
    ];

    public function fields()
    {
        return $this->hasMany('App\SubscriberField');
    }
}
