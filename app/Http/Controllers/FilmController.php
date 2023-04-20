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

