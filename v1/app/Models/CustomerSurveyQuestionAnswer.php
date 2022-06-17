<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSurveyQuestionAnswer extends Model
{
    use HasFactory;

	protected $table = 'customer_survey_question_answer';

	protected $fillable = [
		'phone',
		'email',
		'id_customer_survey_question',
		'answer',
	];
}
