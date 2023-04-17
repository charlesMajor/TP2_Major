<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;



class UserTest extends TestCase
{
    //use DatabaseMigrations si ça ne fonctionne pas
    use RefreshDatabase;   
}
