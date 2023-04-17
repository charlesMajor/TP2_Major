<?php

namespace App\Http\Controllers;

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
        return "Create film";
    }

    public function update($id, Request $request)
    {
        return "Update film";
    }

    public function destroy($id, Request $request)
    {
        return "Destroy film";
    }
}

