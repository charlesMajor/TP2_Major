<?php

namespace App\Repository;

use App\Repository\Interfaces\ActorRepositoryInterface;
use App\Models\Actor;

class ActorRepository implements ActorRepositoryInterface
{
    public function create(array $content)
    {
        return Actor::create($content);
    }

    public function getAll($perPage = 0)
    {
        return Actor::paginate($perPage);
    }

    public function getById($id)
    {
        return Actor::findOrFail($id);
    }

    public function update($id, array $content)
    {
        $actor = Actor::findOrFail($id);
        $actor->delete();
        $actor->insert($content);   
    }

    public function delete($id)
    {
        $actor = actor::findOrFail($id);  
        $actor->delete();  
    }
}