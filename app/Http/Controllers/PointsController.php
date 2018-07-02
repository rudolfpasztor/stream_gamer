<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\Point;
use Illuminate\Support\Facades\Input;
use App\EndUser;
use App\Http\Controllers\EndUserController;
use App\Http\Controllers\SettingsController;
use App\Http\Resources\PointResource as PointResource;

class PointsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get point list
        $points = Point::orderby('created_at', 'desc')->with('campaign', 'owner')->paginate(15);
        //return collection as resource
        return PointResource::collection($points);
        //return $points;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $point = $request->isMethod('put') ? Point::findOrFail($request->id) : new Point;

        $point->id = $request->input('id');
        $point->end_user_id = $request->input('end_user_id');
        $point->campaign_id = $request->input('campaign_id');
        $point->source = $request->input('source');
        //$point->user_id = (is_int($request->input('id'))) ? $request->input('user_id') : DB::table('users')->where('nick', $this->id)->first()->value('id');
        $point->count = $request->input('count');

        if($point->save()) {
            return new PointResource($point);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Get Points
        $point = Point::with('campaign')->with('user')->findOrFail($id);
        //return new PointResource($points);
        return $point;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return
     */
    public function destroy($id)
    {
        $point = Point::findOrFail($id);

        if(!$point) {
            return [
                'status' => 'error',
                'status_msg' => 'Points not found with id of #'. $id,
            ];
        }

        if ($point->delete()){
            return new PointResource($point);
        }
    }

    public function sum()
    {   $api_token = Input::get('api_token');
        $user = EndUser::with('sumPoints')->where('api_token', $api_token)->first();
        if(!$user) {
            return [
                "status" => "error",
                "status_msg" => "nincs ilyen user"
            ];
        }
        return $user;
    }
    /**
     * Add points to every user in database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function addPointsToAll(Request $request) {

        $users = DB::table('end_users')->get();
        $campaign_id = $request->input('campaign_id');
        $source = $request->input('source');
        $count = $request->input('count');
        $timestamp = new \DateTime();

        $points = [];

        foreach ($users as $user) {
            $points[] = [
                'end_user_id' => $user->id,
                'campaign_id' => $campaign_id,
                'source' => $source,
                'count' => $count,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ];
        }

        if(Point::insert($points)) {

            return [
                'status' => 'success',
                'point_count' => count($points)
            ];
        }


        return count($points);
    }

    public function addPointsToOnline(Request $request) {
        $last_activity = '';
        $timestamp = new \DateTime();
        $online_array = app('App\Http\Controllers\EndUserController')->checkOnlineUsers($request->channel);

        if( empty($online_array['registered_users']) ) {

            $last_activity = $timestamp->format('Y-m-d H:i:s') . ' | chatbot run correctly, but there is no registered users online.';
            app('App\Http\Controllers\SettingsController')->set('last_activity', $last_activity);

            return [
               'status' => 'success',
               'status_msg' => 'No registered user in the viewer list',
               'stuff' => $online_array
           ];
        }

        $users = EndUser::findMany($online_array['registered_users']);

        $campaign_id = $request->input('campaign_id');
        $source = $request->input('source');
        $count = $request->input('count');
        $timestamp = new \DateTime();

        foreach ($users as $user) {
            $points[] = [
                'end_user_id' => $user->id,
                'campaign_id' => $campaign_id,
                'source' => $source,
                'count' => $count,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ];
        }

        if(Point::insert($points)) {
            $last_activity = $timestamp->format('Y-m-d H:i:s') . ' | give '. $count .' points to '.count($users) .' registered users from '.$online_array['chatter_count'] . ' online';
            app('App\Http\Controllers\SettingsController')->set('last_activity', $last_activity);
            return [
                'status' => 'success',
                'online_count' => $online_array['chatter_count'],
                'status_msg' => 'pontok hozzáadva',
                'count' => count($points),
            ];
        }

        return $online_array;

    }
}
