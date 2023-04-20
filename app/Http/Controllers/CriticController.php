<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use Illuminate\Http\Request;
use App\Models\Critic;
use App\Http\Resources\CriticResource;
use App\Repository\CriticRepository;

class CriticController extends Controller
{
    private CriticRepository $criticRepository;

    public function __construct(CriticRepository $criticRepository)
    {
        $this->criticRepository = $criticRepository;
    }

    public function create(Request $request)
    {
        try
        {
            $user = Auth::User();

            $validator = Validator::make($request->all(), [
                'score' => 'required',
                'comment' => 'required',
                'film_id' => 'required'
            ]);

            if($validator->fails())
            {
                abort(INVALID_DATA, 'Invalid data');
            }
            
            $critic = $this->criticRepository->create(["score" => $request->score, "comment" => $request->comment, 
            "user_id" => $user->id, "film_id" => $request->film_id]);
            return (new CriticResource($critic))->response()->setStatusCode(CREATED);
        }
        catch(Exception $ex)
        {
            abort(SERVER_ERROR, 'Server error');
        }
    }
}
