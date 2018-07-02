<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagesController extends Controller
{
    public function getHome() {
        return view('home');
    }

    public function getDashboard() {
        return view('dashboard');
    }

    public function getPoints() {
        return view('points');
    }

    public function getCampaigns() {
        return view('campaigns');
    }

    public function getEndUsers() {
        return view('end_users');
    }

    public function getChatBot() {
        return view('chatbot');
    }

    public function getDrops() {
        return view('drops');
    }
    public function getQuestions() {
        return view('questions');
    }


    public function getCasparWinner($name){
        $winner = DB::table('end_users')->where('nick', $name)->first();
        return view('caspar/winner')->with('winner', $winner);
    }

    public function getCasparChatbotStatus(){
        $setting = Setting::where('key', 'chatbot_run')->first();
        return view('caspar/chatbotstatus')->with('status', $setting->value);
    }
}
