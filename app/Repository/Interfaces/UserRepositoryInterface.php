<?php

namespace App\Repository\Interfaces;

use App\Repository\Interfaces\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface 
{
    public function editPassword($id, array $content);
}
