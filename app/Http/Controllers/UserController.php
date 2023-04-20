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

    public function show($id, Request $request)
    {
        try
        {
            $user = $this->userRepository->getById($id);
            return (new UserResource($user))->response()->setStatusCode(OK);
        }
        catch(QueryException $ex)
        {
            abort(NOT_FOUND, 'Invalid Id');
        }
        catch(Exception $ex)
        {
            abort(SERVER_ERROR, 'Server error');
        }
    }

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

            try
            {
                $this->userRepository->editPassword($id, $request->all());
            }
            catch(QueryException $ex)
            {
                abort(NOT_FOUND, 'Invalid Id');
            }
            
            return response(null, OK);
        }
        catch(Exception $ex)
        {
            abort(SERVER_ERROR, 'Server error');
        }
    }
}
