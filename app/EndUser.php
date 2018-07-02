<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class EndUser extends Model
{
    //
    protected $fillable = [
        'nick', 'twitch_id', 'foreign_user_id', 'email', 'avatar_url'
    ];

    public function point() {
        return $this->hasMany('App\Point');
    }

    public function sumPoints()
    {
        $points = $this->hasOne('App\Point')
            ->selectRaw('sum(points.count) as sum, points.end_user_id')
            ->groupBy('end_user_id')
            ->orderBy('sum', 'desc');

        if(!$points) {
            $points = [
                "sum" => 0,
                "end_user_id" => ""
            ];
        }

        return $points;

    }

    public function getExperiencePoints()
    {
        return $this->point()->sum('count');
    }

    public function scopeOrderByPointSum($query, $order = 'desc')
    {
        return $query->leftJoin('points', 'points.end_user_id', '=', 'end_users.id')
            ->groupBy('end_users.id')
            ->addSelect(['*', \DB::raw('sum(count) as sum')])
            ->orderBy('sum', $order);
    }

    /**
     * An alternative relationship with comment votes for DB count queries
     */
    public function pointRelation()
    {
        return $this->hasOne('App\Point')
            ->selectRaw('end_user_id, sum(count) as sum')
            ->groupBy('end_user_id');
    }


    /**
     * @return int the mutator to get the sum of all the comment votes
     */
    public function getPointAttribute()
    {
        // if relation is not loaded already, let's do it first
        if (!array_key_exists('pointRelation', $this->relations)) {
            $this->load('pointRelation');
        }

        $related = $this->getRelation('pointRelation');

        // then return the count directly
        return ($related) ? (int) $related->sum : 0;
    }

}
