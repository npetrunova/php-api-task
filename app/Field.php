<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $fillable = ['title', 'type'];

    public static $acceptedTypes = ['date', 'number', 'boolean', 'string'];
}
