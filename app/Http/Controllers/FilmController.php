<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use Illuminate\Http\Request;
use App\Http\Resources\FilmResource;
use App\Models\Film;
use App\Repository\FilmRepository;


class FilmController extends Controller
{   
    private FilmRepository $filmRepository;

    public function __construct(FilmRepository $filmRepository)
    {
        $this->filmRepository = $filmRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/films",
     *     tags={"Films"},
     *     summary="Creates a film if the user is an admin",
     *     description="Maximum of 60 calls per minute",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="release_year",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="length",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="rating",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="special_features",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="language_id",
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
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'release_year' => 'required',
                'length' => 'required',
                'description' => 'required',
                'rating' => 'required',
                'special_features' => 'required',
                'image' => 'required',
                'language_id' => 'required'
            ]);

            if($validator->fails())
            {
                abort(INVALID_DATA, 'Invalid data');
            }

            $film = $this->filmRepository->create($request->all());
            return (new FilmResource($film))->response()->setStatusCode(CREATED);
        }
        catch(Exception $ex)
        {
            abort(SERVER_ERROR, 'Server error');
        }
    }

    /**
     * @OA\Put(
     *     path="/api/films/{id}",
     *     tags={"Films"},
     *     summary="Updates all the infos of a film if the user is an admin",
     *     description="Maximum of 60 calls per minute",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="release_year",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="length",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="rating",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="special_features",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="language_id",
     *                     type="int"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="Id of film to update",
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
     *       response = 404,
     *       description = "Not found"),
     *     @OA\Response(
     *       response = 500,
     *       description = "Server error")
     * )
     */
    public function update($id, Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'release_year' => 'required',
                'length' => 'required',
                'description' => 'required',
                'rating' => 'required',
                'special_features' => 'required',
                'image' => 'required',
                'language_id' => 'required'
            ]);

            if($validator->fails())
            {
                abort(INVALID_DATA, 'Invalid data');
            }

            try
            {
                $this->filmRepository->update($id, $request->all());
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

    /**
     * @OA\Delete(
     *     path="/api/films/{id}",
     *     tags={"Films"},
     *     summary="Deletes a film if the user is an admin",
     *     description="Maximum of 60 calls per minute",
     *     @OA\Parameter(
     *         description="Id of film to delete",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="int")
     *     ),
     *     @OA\Response(
     *        response = 204,
     *        description = "No content"),
     *     @OA\Response(
     *       response = 401,
     *       description = "Unauthorized"),
     *     @OA\Response(
     *       response = 403,
     *       description = "Forbidden"),
     *     @OA\Response(
     *       response = 404,
     *       description = "Not found"),
     *     @OA\Response(
     *       response = 500,
     *       description = "Server error")
     * )
     */
    public function destroy($id)
    {
        try
        {
            $this->filmRepository->delete($id);
            return response(null, NO_CONTENT);
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
}

