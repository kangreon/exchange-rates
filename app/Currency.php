<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $casts = [
        'id' => 'string'
    ];

    function rates() {
        return $this->hasMany('App\Rate');
    }
}
