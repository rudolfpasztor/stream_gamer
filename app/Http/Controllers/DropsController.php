<?php

namespace App\Http\Controllers;

use App\Drop;
use App\Point;
use App\EndUser;
use App\Questions;
use App\Ticket;
use App\Http\Resources\PointResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Events\NewTicket;

class DropsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $drops = Drop::orderby('created_at', 'desc')->with(['campaign', 'ticket_count', 'question_gate'])->paginate(15);
        return $drops;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $drop = $request->isMethod('put') ? Drop::findOrFail($request->input('id')) : new Drop;

        $drop->id = $request->input('id');
        $drop->name = $request->input('name');
        $drop->qty = 1;
        $drop->status = 'passive';
        $drop->image_url = $request->input('image_url');
        $drop->campaign_id = $request->input('campaign_id');
        $drop->question_id = $request->input('question_id');
        $drop->winners = $request->input('winners');
        $drop->price = $request->input('price');

        if($drop->save()) {
            return $drop;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $drop = Drop::findOrFail($id);

        if(!$drop) {
            return [
                'status' => 'error',
                'status_msg' => 'Drop not found with id of #'. $id,
            ];
        }

        if ($drop->delete()){
            return $drop;
        }
    }

    public function activateDrop($id) {
        DB::table('drops')->where('winners', '0')->where('id', $id)->update(['status' => 'active']);
        return [
            "status" => "success"
        ];
    }

    public function deactivateDrop($id) {
        DB::table('drops')->where('id', $id)->update(['status' => 'passive']);
        return [
            "status" => "success"
        ];
    }


    public function activateAll() {
        DB::table('drops')->where('winners', '0')->update(['status' => 'active']);
        return [
            "status" => "success"
        ];
    }

    public function deactivateAll() {
        DB::table('drops')->update(['status' => 'passive']);
        return [
            "status" => "success"
        ];
    }

    public function activeDrops() {
        $drops = Drop::orderby('created_at', 'desc')->where('status', 'active')->get();
        return $drops;
    }

    public function buyDrop(Request $request){

        $id = Input::get('drop_id');
        $qty = Input::get('qty');
        $api_token = Input::get('api_token');
        $answer_id = Input::get('answer_id');

        $user = EndUser::with('sumPoints')->where('api_token', $api_token)->first();
        if(!$user) {
            return [
                "status" => "error",
                "status_msg" => "nincs ilyen user"
            ];
        }

        $user_id = $user->id;

        $drop = Drop::findOrFail($id);
        $drop_value = $drop->price;
        $total = $qty * $drop_value;

        $user_wallet = DB::table('points')->where('end_user_id', $user_id)->sum('count');

        if($drop->status == 'passive') {
            return [
                "status" => "error",
                "status_msg" => "A vásárlási periódus lezárult."
            ];
        }

        $question = Questions::find($drop->question_id);
        if($answer_id === $question->correct_answer_id) {
            $question->correct_answers ++;
            $question->save();
        } else {
                $question->incorrect_answers ++;
                $question->save();
                return [
                    "status" => "error",
                    "status_msg" => "Sajnos nem sikerült helyesen válaszolnod! Próbáld újra!",
                    "correct_answer_id" => $question->correct_answer_id,
                    "answer" => $answer_id
                ];
        }

        if($user_wallet >= $total) {

            $point = new Point;
            $point->end_user_id = $user_id;
            $point->campaign_id = $drop->campaign_id;
            $point->source = ($answer_id === $question->correct_answer_id) ? 'Buying ' . $drop->name : 'Tried to buy ' . $drop->name . ' but failed!';
            $point->count = $total * -1;


            if($point->save()) {

                $ticket_counter = 0;
                $tickets = array();
                for ($i = 0; $i <= $qty-1; $i++){
                    $ticket = new Ticket;
                    $ticket->end_user_id = $user_id;
                    $ticket->nick = $user->nick;
                    $ticket->drop_id = $drop->id;
                    if($ticket->save()) {
                        $ticket_counter ++;
                        array_push($tickets, $ticket);
                    }
                }

                $user_wallet = DB::table('points')->where('end_user_id', $user_id)->sum('count');
                //broadcast new event
                //event( new NewTicket($tickets) );
                return [
                    "id" => $id,
                    "status" => "success",
                    "status_msg" => "Sikeres vásárlás!",
                    "drop" => $drop,
                    "qty" => $ticket_counter,
                    "tickets" => $tickets,
                    "user_wallet" => $user_wallet
                ];

            } else {
                return [
                    "status" => "error",
                    "status_msg" => "hiba lépett fel az érték levonása közben. Próbáld újra"
                ];
            }

        } else {
            return [
                "status" => "error",
                "status_msg" => "Nincs elég pontod"
            ];
        }

        return [
            "user"  => $user,
            "user_wallet" => $user_wallet,
            "drop_price"  => $drop_value
        ];
    }
}
