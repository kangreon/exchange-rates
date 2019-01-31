<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $casts = [
        'currency_id' => 'string'
    ];

    protected $fillable = [
        'date',
        'value'
    ];

    protected $primaryKey = 'id';
}
