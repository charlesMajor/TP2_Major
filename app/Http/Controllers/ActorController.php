<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\ActorRepository;

class ActorController extends Controller
{
    private ActorRepository $actorRepository;

    public function __construct(ActorRepository $actorRepository)
    {
        $this->actorRepository = $actorRepository;
    }
}
