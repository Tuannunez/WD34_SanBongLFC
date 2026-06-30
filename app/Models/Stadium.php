<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stadium extends Model
{
    protected $fillable = [
        'name',
        'image',
        'address',
        'phone',
        'email',
        'open_time',
        'close_time',
    ];
}
