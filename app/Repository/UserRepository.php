<?php

namespace App\Repository;

use App\Repository\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $content)
    {
        return User::create($content);
    }

    public function getAll($perPage = 0)
    {
        return User::paginate($perPage);
    }

    public function getById($id)
    {
        return User::findOrFail($id);
    }

    public function update($id, array $content)
    {
        $user = User::findOrFail($id);
        $user->delete();
        $user->insert($content);   
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);  
        $user->delete();  
    }

    public function editPassword($id, array $content)
    {
        $user = User::findOrFail($id);
        $user->update(['password' => bcrypt($content["password"])]);
    }
}