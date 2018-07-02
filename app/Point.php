<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{

    protected $fillable = array(
        'count',
        'end_user_id',
        'source',
        'campaign_id',
    );

    public function owner() {
        return $this->hasOne('App\EndUser', 'id', 'end_user_id');
    }

    public function campaign()
    {
        return $this->belongsTo('App\Campaign');
    }
}
