<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Questions extends Model
{
    //
    protected $fillable = [
        'question', 'answers', 'correct_answer_id',
    ];

    public function drop() {
        return $this->belongsTo('drops');
    }
}
