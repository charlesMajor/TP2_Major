<?php

namespace App\Repository;

use App\Repository\Interfaces\CriticRepositoryInterface;
use App\Models\Critic;

class CriticRepository implements CriticRepositoryInterface
{
    public function create(array $content)
    {
        return Critic::create($content);
    }

    public function getAll($perPage = 0)
    {
        return Critic::paginate($perPage);
    }

    public function getById($id)
    {
        return Critic::findOrFail($id);
    }

    public function update($id, array $content)
    {
        $critic = Critic::findOrFail($id);
        $critic->delete();
        $critic->insert($content);   
    }

    public function delete($id)
    {
        $critic = Critic::findOrFail($id);  
        $critic->delete();  
    }
}