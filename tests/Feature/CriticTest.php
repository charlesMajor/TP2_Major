<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Film;
use App\Models\Critic;
use App\Models\Language;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use Laravel\Sanctum\Sanctum;
use Auth;

class CriticTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_userCanCreateCritic()
    {
        $this->seed();

        $jsonNorm = ["login"=>"norm",
        "password"=>"norm",
        "password_confirmation"=>"norm",
        "email"=>"notadmin@hotmail.com",
        "last_name"=>"Admin",
        "first_name"=>"Not",
        "role_id"=>1];
        $this->postJson('/api/signup', $jsonNorm);
        $norm = Auth::User();

        Sanctum::actingAs(
            $norm
        );

        $json = ["score" => 10,
        "comment" => "test comment",
        "film_id" => 1];
        $response = $this->postJson('/api/critics', $json);

        $response->assertJsonFragment($json);
        $response->assertStatus(CREATED);
        $this->assertDatabaseHas('critics', $json);
    }

    public function test_creatingAFilmShouldReturn422WhenMissingField()
    {
        $this->seed();

        $jsonNorm = ["login"=>"norm",
        "password"=>"norm",
        "password_confirmation"=>"norm",
        "email"=>"notadmin@hotmail.com",
        "last_name"=>"Admin",
        "first_name"=>"Not",
        "role_id"=>1];
        $this->postJson('/api/signup', $jsonNorm);
        $norm = Auth::User();

        Sanctum::actingAs(
            $norm
        );

        $json = ["comment" => "test comment",
        "film_id" => 1];
        $response = $this->postJson('/api/critics', $json);

        $response->assertStatus(INVALID_DATA);
    }

    public function test_userTryingToCreateTwoCriticsForOneFilmShouldReturnForbidden()
    {
        $this->seed();

        $jsonNorm = ["login"=>"norm",
        "password"=>"norm",
        "password_confirmation"=>"norm",
        "email"=>"notadmin@hotmail.com",
        "last_name"=>"Admin",
        "first_name"=>"Not",
        "role_id"=>1];
        $this->postJson('/api/signup', $jsonNorm);
        $norm = Auth::User();

        Sanctum::actingAs(
            $norm
        );

        $json = ["score" => 10,
        "comment" => "test comment",
        "film_id" => 1];
        $this->postJson('/api/critics', $json);

        $json = ["score" => 5,
        "comment" => "test comment 2",
        "film_id" => 1];
        $response = $this->postJson('/api/critics', $json);

        $response->assertStatus(FORBIDDEN);
    }

    public function test_tryingToCreateFilmWithoutBeingConnectedShouldReturnUnauthorized()
    {
        $this->seed();

        $json = ["score" => 10,
        "comment" => "test comment",
        "film_id" => 1];
        $response = $this->postJson('/api/critics', $json);

        $response->assertStatus(UNAUTHORIZED);
    }

    public function test_goingOverLimitOfCreateCallShouldReturnCode429()
    {
        $this->seed();

        $jsonNorm = ["login"=>"norm",
        "password"=>"norm",
        "password_confirmation"=>"norm",
        "email"=>"notadmin@hotmail.com",
        "last_name"=>"Admin",
        "first_name"=>"Not",
        "role_id"=>1];
        $this->postJson('/api/signup', $jsonNorm);
        $norm = Auth::User();

        Sanctum::actingAs(
            $norm
        );

        $json = ["score" => 10,
            "comment" => "test comment 2",
            "film_id" => 1];

        for ($i = 0; $i < THROTTLING_ROUTES; $i++)
        {
            $this->postJson('/api/critics', $json);
        }
        
        $response = $this->postJson('/api/critics', $json);

        $response->assertStatus(TOO_MANY_ATTEMPTS);
    }
   
}
