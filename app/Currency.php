<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    //
    public function points() {
        return $this->hasMany('App\Points');
    }
}
