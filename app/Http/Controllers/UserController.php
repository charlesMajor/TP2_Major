<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\LanguageResource;
use App\Models\User;
use App\Models\Language;
use Illuminate\Support\Facades\Validator;
use App\Repository\UserRepository;

class UserController extends Controller
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Gets the infos of an user if he's the one connected",
     *     description="Maximum of 60 calls per minute",
     *     @OA\Parameter(
     *         description="Id of the user to get infos on",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="int")
     *     ),
     *     @OA\Response(
     *        response = 200,
     *        description = "Ok"),
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
    public function show($id, Request $request)
    {
        try
        {
            $user = $this->userRepository->getById($id);
            return (new UserResource($user))->response()->setStatusCode(OK);
        }
        catch(Exception $ex)
        {
            abort(SERVER_ERROR, 'Server error');
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Updates the password of an user if he's the one connected",
     *     description="Maximum of 60 calls per minute",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="Id of the user to update",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="int")
     *     ),
     *     @OA\Response(
     *        response = 200,
     *        description = "Ok"),
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
    public function edit($id, Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'password' => 'required',
                'password_confirmation' => 'required_with:password|same:password',
            ]);

            if($validator->fails())
            {
                abort(INVALID_DATA, 'Invalid data');
            }

            $this->userRepository->editPassword($id, $request->all());
            
            return response(null, OK);
        }
        catch(Exception $ex)
        {
            abort(SERVER_ERROR, 'Server error');
        }
    }
}
