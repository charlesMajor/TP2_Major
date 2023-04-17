<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Critic;
use App\Repository\CriticRepository;

class CriticController extends Controller
{
    private CriticRepository $criticRepository;

    public function __construct(CriticRepository $criticRepository)
    {
        $this->criticRepository = $criticRepository;
    }

    public function create(Request $request)
    {
        return "Create critic";
    }
}
