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

    /**
     * @OA\Post(
     *     path="/api/critics",
     *     tags={"Critics"},
     *     summary="Creates a critic, an user cannot do more than one per film",
     *     description="Maximum of 60 calls per minute",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="score",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="comment",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="film_id",
     *                     type="int"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *        response = 201,
     *        description = "Created"),
     *     @OA\Response(
     *       response = 422,
     *       description = "Invalid data"),
     *     @OA\Response(
     *       response = 401,
     *       description = "Unauthorized"),
     *     @OA\Response(
     *       response = 403,
     *       description = "Forbidden"),
     *     @OA\Response(
     *       response = 500,
     *       description = "Server error")
     * )
     */
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
