<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Repository\UserRepository;

class AuthController extends Controller
{

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/signup",
     *     tags={"Authentification"},
     *     summary="Creates a new user and token",
     *     description="Maximum of 5 calls per minute",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="login",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="first_name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="role_id",
     *                     type="int"
     *                 ),
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
     *       response = 500,
     *       description = "Server error")
     * )
     */
    public function register(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'login' => 'required',
                'password' => 'required',
                'password_confirmation' => 'required_with:password|same:password',
                'email' => 'unique:users',
                'email' => 'required',
                'email' => 'email:rfc',
                'last_name' => 'required',
                'first_name' => 'required',
                'role_id' => 'required',
                'role_id' => 'in:1,2'
            ]);

            if($validator->fails())
            {
                abort(INVALID_DATA, 'Invalid data');
            }

            $user = $this->userRepository->create([
                'login' => $request->login,
                'password' => bcrypt($request->password),
                'email' => $request->email,
                'last_name' => $request->last_name,
                'first_name' => $request->first_name,
                'role_id' => $request->role_id]);
            
            if(
                !Auth::attempt([
                    'login' => $request->login,
                    'password' => $request->password,
                    'email' => $request->email,
                    'last_name' => $request->last_name,
                    'first_name' => $request->first_name,
                    'role_id' => $request->role_id
                ]))
                {
                    abort(500, 'error');
                }
            

            $token = $user->createToken('userToken');
            return response()->json(['userToken' => $token->plainTextToken])->setStatusCode(CREATED);
        }
        catch(Exception $ex)
        {
            abort(SERVER_ERROR, 'Server error');
        }   
    }


    /**
     * @OA\Post(
     *     path="/api/signin",
     *     tags={"Authentification"},
     *     summary="Connect to an user in bd and creates a token",
     *     description="Maximum of 5 calls per minute",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
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
     *       response = 500,
     *       description = "Server error")
     * )
     */
    public function login(Request $request)
    {       
        try
        {
            $validator = Validator::make($request->all(), [
                'email' => 'required',                
                'password' => 'required'
            ]);

            if($validator->fails())
            {
                abort(INVALID_DATA, 'Invalid data');
            }
            
            if(Auth::attempt([
                    'email' => $request->email,
                    'password' => $request->password
                ]))
                {
                    $user = Auth::User();
                    $token = $user->createToken('userToken');
                    return response()->json(['userToken' => $token->plainTextToken])->setStatusCode(OK);
                }
                else
                {
                    abort(UNAUTHORIZED, 'Unauthorized');
                }
        }
        catch(Exception $ex)
        {
            abort(SERVER_ERROR, 'Server error');
        }   
    }


    /**
    *@OA\Post(
    *   path="/api/signout",
    *   tags={"Authentification"},
    *   summary="Deletes all existing tokens for user",
    *   description="Maximum of 5 calls per minute",
    *   @OA\Response(
    *       response = 204,
    *       description = "No content")
    *   ),
    *   @OA\Response(
    *       response = 500,
    *       description = "Server error")
    */
    public function logout()
    {     
        try
        {
            if(Auth::Check())
            {
                $user = Auth::User();
                foreach($user->tokens as $token)
                {
                    $token->delete();
                }
                return response(null, NO_CONTENT);
            }
        }  
        catch(Exception $ex)
        {
            abort(SERVER_ERROR, 'Server error');
        }
        
    }
}
