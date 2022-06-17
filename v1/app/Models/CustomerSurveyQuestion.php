<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSurveyQuestion extends Model
{
    use HasFactory;

	protected $table = 'customer_survey_question';

	protected $fillable = [
		'question_type',
		'question',
		'required',
	];
}
