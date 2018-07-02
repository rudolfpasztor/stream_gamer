<?php

namespace App\Http\Controllers;

use App\EndUser;
use App\Http\Resources\EndUserResource;
use App\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EndUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return EndUserResource
     */
    public function index()
    {
        $users = DB::table('end_users')
            ->select('end_users.*')
            ->leftJoin('points', 'end_users.id', '=', 'points.end_user_id')
            ->addSelect(DB::raw('SUM(points.count) as sum_points'))
            ->groupBy('end_users.id')
            ->selectRaw('sum(points.count) as sum, points.end_user_id')
            ->groupBy('end_user_id')
            ->orderBy('sum_points', 'desc')
            ->paginate(15);
        return $users;

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
     * @return EndUserResource | array
     */
    public function store(Request $request)
    {

        $user = $request->isMethod('put') ? EndUser::findOrFail($request->id) : new EndUser;

        $find_by_email = DB::table('end_users')->where('email', $request->input('email'))->first();

        if($find_by_email) {
            return [
                'exist' => true,
                'user' => $find_by_email
            ];
        }
        //we have a nick, but different email, lets update that user
        $find_by_name = EndUser::where('nick', $request->input('nick'))->first();

        if($find_by_name) {

            $find_by_name->nick = $request->input('nick');
            $find_by_name->twitch_id = $request->input('twitch_id');
            $find_by_name->foreign_user_id = $request->input('foreign_user_id');
            $find_by_name->avatar_url = $request->input('avatar_url');
            $find_by_name->email = $request->input('email');

            if($find_by_name->save()) {
                return new EndUserResource($find_by_name);
            }
        }

        $user->id = $request->input('id');
        $user->nick = $request->input('nick');
        $user->twitch_id = $request->input('twitch_id');
        $user->foreign_user_id = $request->input('foreign_user_id');
        $user->avatar_url = $request->input('avatar_url');
        $user->email = $request->input('email');
        $user->api_token = bin2hex(openssl_random_pseudo_bytes(30));

        if($user->save()) {
            return new EndUserResource($user);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EndUser  $endUser
     * @return \Illuminate\Http\Response | array
     */
    public function show($id)
    {
        try
        {
            $user = DB::table('end_users')
                ->select('end_users.*')
                ->where('end_users.id', $id)
                ->leftJoin('points', 'end_users.id', '=', 'points.end_user_id')
                ->addSelect(DB::raw('SUM(points.count) as sum_points'))
                ->groupBy('end_users.id')
                ->get();

            return $user;


        }
        catch(ModelNotFoundException $e) {
            return [
                'error_code'    => 404,
                'error_msg'     => 'User with id of #'.$id.' not found'
            ];
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EndUser  $endUser
     * @return \Illuminate\Http\Response
     */
    public function edit(EndUser $endUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EndUser  $endUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EndUser $endUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EndUser  $endUser
     * @return EndUserResource | array
     */
    public function destroy($id)
    {
        try
        {
            $user = EndUser::findOrFail($id);
            if ($user->delete()){
                return new EndUserResource($user);
            }
        }
        catch(ModelNotFoundException $e) {
            return [
                'error_code'    => 404,
                'error_msg'     => 'User with id of #'.$id.' not found. Cannot delete.'
            ];
        }
    }

    /**
     * Import users from CSV
     *
     * @param  Request
     * @return EndUserResource | array
     */
    public function importFromCSV() {
        $usernames = array();
        if (($handle = fopen ( public_path () . '/top.csv', 'r' )) !== FALSE) {
            while ( ($data = fgetcsv ( $handle, 1000, ',' )) !== FALSE ) {

                array_push($usernames, $data );

            }
            fclose ( $handle );
        }

        $new_users = array();
        $existing_users = array();
        //loop through users to chec against database
        foreach ($usernames as $key => $user) {
            $name = $user[0];
            $user = DB::table('end_users')->where('nick', $name)->first();

            if(!$user) {
                $user = new EndUser;
                $user->nick = $name;
                $user->twitch_id = 0;
                $user->foreign_user_id = 0;
                $user->email = $name.'@asd.hu';
                $user->avatar_url = '';
                $user->api_token = bin2hex(openssl_random_pseudo_bytes(30));
                if($user->save()) {
                   array_push($new_users, $name);
                }
            } else {
                array_push($existing_users, $name);
            }

            //return $user[0];
        }

        return [
            'existing_users' => $existing_users,
            'new_users'=> $new_users
        ];

    }

    /**
     * Import users from CSV
     *
     * @param  Request
     * @return EndUserResource | array
     */
    public function importPointsFromCSV() {
        $usernames = array();
        if (($handle = fopen ( public_path () . '/top.csv', 'r' )) !== FALSE) {
            while ( ($data = fgetcsv ( $handle, 1000, ',' )) !== FALSE ) {

                array_push($usernames, $data );

            }
            fclose ( $handle );
        }

        $user_points = array();
        foreach ($usernames as $key => $user) {
            $name = $user[0];
            $point_count = $usernames[$key][1];
            $user = DB::table('end_users')->where('nick', $name)->first();

            if(!$user) {

            } else {
                $point = new Point;
                $point->end_user_id = $user->id;
                $point->campaign_id = 1;
                $point->source = 'old points';
                $point->count = $point_count;

                if($point->save()) {
                    array_push($user_points, [$name, $point_count]);
                }
            }

        }


    }

    public function checkOnlineUsers($chanel) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://tmi.twitch.tv/group/user/gamerhutv/chatters",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                // Set Here Your Requesred Headers
                'Content-Type: application/json',
                //'Authorization: Bearer '.$oauth_token,
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);


        if ($err) {
            return $err;
        }

        $json = json_decode($response);
        $chatter_count = $json->chatter_count;
        $user_names =  $json->chatters->viewers;

        return [
            'chatter_count' => $chatter_count,
            'user_names' => $user_names,
            'registered_users' => EndUserController::getRegisteredUsersFromList($user_names)
        ];

    }

    public function getRegisteredUsersFromList($user_names){

        $registered_users = [];
        $counter = 0;
        foreach ($user_names as $user_name) {
            $user = DB::table('end_users')->where('nick', strtolower($user_name))->first();
            if($user){
                $counter++;
                $registered_users[] = $user->id;
            }
        }

        return $registered_users;
    }

    /**
     * Process multiple users from chatbot
     *
     * @param  string
     * @return array
     */
    public function massCheck($chanel)
    {

        return $chanel;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://tmi.twitch.tv/group/user/".$chanel."/chatters",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                // Set Here Your Requesred Headers
                'Content-Type: application/json',
                //'Authorization: Bearer '.$oauth_token,
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return $response;
        }

        $user_names = $response;

        $registered_users = [];
        $unregistered_users = [];
        foreach ($user_names as $user_name) {
            $user = DB::table('end_users')->where('nick', $user_name)->first();
            if($user){
                array_push($registered_users, $user->id);
            } else {
                array_push($unregistered_users, $user_name);
            }
        }

        return $registered_users;
    }

    public function massProcess($unregistered_users = null, $user_names = null)
    {

        $client_id = 'qctqnbpgbth2vlgdu49ta2xajozvms';
        $oauth_token = '6tdhsbcc7q9khyd41jmkb70zb0827i';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.twitch.tv/helix/users?login=rudolfp&yydx_",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                // Set Here Your Requesred Headers
                'Content-Type: application/json',
                'Authorization: Bearer '.$oauth_token,
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }
}
