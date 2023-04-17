<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\LanguageRepository;

class LanguageController extends Controller
{
    private LanguageRepository $languageRepository;

    public function __construct(LanguageRepository $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }
}
