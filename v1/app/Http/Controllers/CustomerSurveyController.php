<?php

namespace App\Http\Controllers;

use App\Models\CustomerSurveyQuestion;
use App\Models\CustomerSurveyQuestionAnswer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerSurveyController extends Controller
{
    public function index(Request $request)
	{
		$token = $request->key;

        if (is_null($token) OR empty($token) ){
            $response = [
                'success'=> false,
                'message'=> 'Token cannot be null'
            ];
        }else{
            $api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();

            if (!$api_key){
                $response = [
                    'success'=> false,
                    'message'=> 'Token mismatch'
                ];
            }else{
				$language_code = !empty($request->language_code) ? $request->language_code : 'id';

				$questions = CustomerSurveyQuestion::where('language_code', $language_code)->get();

				$response = [
					'success' => true,
					'message' => 'List Customer Survey Questions',
					'data' => $questions
				];
			}
		}

		return $response;
	}

	public function store(Request $request)
	{
		$token = $request->key;

        if (is_null($token) OR empty($token) ){
            $response = [
                'success'=> false,
                'message'=> 'Token cannot be null'
            ];
        }else{
			$api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();

            if (!$api_key){
                $response = [
                    'success'=> false,
                    'message'=> 'Token mismatch'
                ];
            }else{
				$rules = [
                    'phone' => ['required'],
                    'email' => ['required'],
					'language_code' => ['required'],
                    'id_customer_survey_question' => ['required'],
                    'answer' => ['required'],
                ];

				$validator = validator()->make($request->all(), $rules);

				if($validator->fails()){
					$errorMessage = $validator->errors()->first();
					$response = [
						'success' => false,
						'message' => $errorMessage
					];
				}else{
					$data = [
						'phone' => $request->phone,
						'email' => $request->email,
						'id_customer_survey_question' => $request->id_customer_survey_question,
						'language_code' => $request->language_code,
						'answer' => $request->answer,
						'data_source' => 'CHAT'
					];

					CustomerSurveyQuestionAnswer::insert($data);

					$response = [
						'success' => true,
						'message' => 'Your information has been saved.'
					];
				}
			}
		}

		return $response;
	}
}
