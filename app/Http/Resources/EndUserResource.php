<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\DB;

class EndUserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /*
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nick' => $this->nick,
            'twitch_id' => $this->twitch_id,
            'foreign_user_id' => $this->foreign_user_id,
            'email' => $this->email,
            'stats' => [
                'points' => DB::table('points')->where('end_user_id', $this->id)->sum('count')
            ]
        ];
        */
        return parent::toArray($request);
    }

}
