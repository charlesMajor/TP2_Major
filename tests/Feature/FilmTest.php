<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Language;
use App\Models\Film;
use Tests\TestCase;

class FilmTest extends TestCase
{
    //use DatabaseMigrations si ça ne fonctionne pas
    use RefreshDatabase;
    
}
