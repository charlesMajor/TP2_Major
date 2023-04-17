<?php

namespace App\Repository;

use App\Repository\Interfaces\LanguageRepositoryInterface;
use App\Models\Language;

class LanguageRepository implements LanguageRepositoryInterface
{
    public function create(array $content)
    {
        return Language::create($content);
    }

    public function getAll($perPage = 0)
    {
        return Language::paginate($perPage);
    }

    public function getById($id)
    {
        return Language::findOrFail($id);
    }

    public function update($id, array $content)
    {
        $language = Language::findOrFail($id);
        $language->delete();
        $language->insert($content);   
    }

    public function delete($id)
    {
        $language = Language::findOrFail($id);  
        $language->delete();  
    }
}