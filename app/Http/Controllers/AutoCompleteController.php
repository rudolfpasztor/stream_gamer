<?php

namespace App\Http\Controllers;

use App\EndUser;
use Illuminate\Http\Request;
use App\EndUsers;
use Illuminate\Support\Facades\Input;

class AutoCompleteController extends Controller
{
    public function index()
    {
        return view('autocomplete');
    }

    public function ajaxData(Request $request){

        $query = Input::get('query');
        $users = EndUser::where('nick','like','%'.$query.'%')->get();
        return response()->json($users);

    }
}
