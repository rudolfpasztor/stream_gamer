<?php

namespace App\Http\Controllers;

use App\Questions;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get questions list
        $questions = Questions::orderby('created_at', 'desc')->paginate(15);
        //return collection as resource
        return $questions;

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
        $question = $request->isMethod('put') ? Questions::findOrFail($request->id) : new Questions;

        $question->id = $request->input('id');
        $question->help_link = ($request->input('help_link')) ? $request->input('help_link') : null;
        $question->question = $request->input('question');
        $question->correct_answers = $request->input('correct_answers');
        $question->incorrect_answers = $request->input('incorrect_answers');
        $question->correct_answer_id = $request->input('correct_answer_id');
        $question->answers = json_encode($request->input('answers'));

        if($question->save()) {
            return $question;
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
        try{
            //Get Points
            $question = Questions::findOrFail($id);
            //return new PointResource($points);
            return $question;
        }
        catch(ModelNotFoundException $e) {
            return [
                'error_code'    => 404,
                'error_msg'     => 'Question with id of #'.$id.' not found'
            ];
        }
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
        $question = Questions::findOrFail($id);

        if(!$question) {
            return [
                'status' => 'error',
                'status_msg' => 'Points not found with id of #'. $id,
            ];
        }

        if ($question->delete()){
            return $question;
        }
    }
}
