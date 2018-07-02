<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Drop extends Model
{
    //
    public function campaign() {
        return $this->hasOne('App\Campaign', 'id', 'campaign_id');
    }

    public function ticket_count() {
        return $this->hasMany('App\Ticket', 'drop_id', 'id');
    }

    public function question_gate() {
        return $this->hasOne('App\Questions', 'id', 'question_id');
    }
}
