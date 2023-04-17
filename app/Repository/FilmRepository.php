<?php

namespace App\Repository;

use App\Repository\Interfaces\FilmRepositoryInterface;
use App\Models\Film;

class FilmRepository implements FilmRepositoryInterface
{
    public function create(array $content)
    {
        return Film::create($content);
    }

    public function getAll($perPage = 0)
    {
        return Film::paginate($perPage);
    }

    public function getById($id)
    {
        return Film::findOrFail($id);
    }

    public function update($id, array $content)
    {
        $film = Film::findOrFail($id);
        $film->delete();
        $film->insert($content);   
    }

    public function delete($id)
    {
        $film = Film::findOrFail($id);  
        $film->delete();  
    }
}