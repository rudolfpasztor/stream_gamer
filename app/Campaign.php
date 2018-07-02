<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    //
    protected $fillable = array(
        'name',
        'currency_name',
    );

    protected $hidden = [
        'status'
    ];

    public function points() {
        return $this->hasMany('App\Points');
    }
}
