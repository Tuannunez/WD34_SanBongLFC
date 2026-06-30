<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stadium extends Model
{
    protected $fillable = [
    'name',
    'image',
    'phone',
    'email',
    'address',
    'open_time',
    'close_time',
    'rating',
    'price',
    'description',
];
}
