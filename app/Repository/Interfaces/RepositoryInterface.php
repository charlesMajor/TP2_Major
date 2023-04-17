<?php

namespace App\Repository\Interfaces;

interface RepositoryInterface
{
    public function create(array $content);
    public function getAll($perPage = 0);
    public function getById($id);
    public function update($id, array $content);
    public function delete($id);
}

?>