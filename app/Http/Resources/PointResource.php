<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\Resource;

class PointResource extends Resource
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
        $campaign = DB::table('campaigns')->where('id', $this->campaign_id)->first();
        $user = DB::table('end_users')->where('id', $this->end_user_id)->first();
        $currency = DB::table('currencies')->where('id', $this->currency_id)->first();
        return [
            'id' => $this->id,
            'count' => $this->count,
            'source' => $this->source,
            'user' => [
                'id' => $user->id,
                'nick'  => $user->nick,
            ],
            'currency' => $currency->name,
            'campaign'  => [
                'id'    => $campaign->id,
                'name'  => $campaign->name,
            ],
            'created_at' => $this->created_at->format('Y.m.d H:i:s'),
        ];*/
        return parent::toArray($request);
    }
}