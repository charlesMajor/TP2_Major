<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Language;
use App\Models\Film;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use Auth;

class FilmTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_adminCanCreateFilm()
    {
        $this->seed();

        $jsonAdmin = ["login"=>"admin",
        "password"=>"admin",
        "password_confirmation"=>"admin",
        "email"=>"admin@hotmail.com",
        "last_name"=>"Admin",
        "first_name"=>"Admin",
        "role_id"=>2];
        $this->postJson('/api/signup', $jsonAdmin);
        $admin = Auth::User();

        Sanctum::actingAs(
            $admin
        );

        $json = ["title" => "test",
        "release_year" => "2000",
        "length" => 120,
        "description" => "test film",
        "rating" => 10,
        "special_features" => "Trailers",
        "image" => "test film image",
        "language_id" => 1];
        $response = $this->postJson('/api/films', $json);

        $response->assertJsonFragment($json);
        $response->assertStatus(CREATED);
        $this->assertDatabaseHas('films', $json);
    }

    public function test_creatingAFilmShouldReturn422WhenMissingField()
    {
        $this->seed();

        $jsonAdmin = ["login"=>"admin",
        "password"=>"admin",
        "password_confirmation"=>"admin",
        "email"=>"admin@hotmail.com",
        "last_name"=>"Admin",
        "first_name"=>"Admin",
        "role_id"=>2];
        $this->postJson('/api/signup', $jsonAdmin);
        $admin = Auth::User();

        Sanctum::actingAs(
            $admin
        );

        $json = ["release_year" => "2000",
        "length" => 120,
        "description" => "test film",
        "rating" => 10,
        "special_features" => "Trailers",
        "image" => "test film image",
        "language_id" => 1];
        $response = $this->postJson('/api/films/', $json);

        $response->assertStatus(INVALID_DATA);
    }

    public function test_normalUserTryingToCreateFilmShouldReturnForbidden()
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

        $json = ["title" => "test",
        "release_year" => "2000",
        "length" => 120,
        "description" => "test film",
        "rating" => 10,
        "special_features" => "Trailers",
        "image" => "test film image",
        "language_id" => 1];
        $response = $this->postJson('/api/films', $json);

        $response->assertStatus(FORBIDDEN);
    }

    public function test_tryingToCreateFilmWithoutBeingConnectedShouldReturnUnauthorized()
    {
        $this->seed();

        $json = ["title" => "test",
        "release_year" => "2000",
        "length" => 120,
        "description" => "test film",
        "rating" => 10,
        "special_features" => "Trailers",
        "image" => "test film image",
        "language_id" => 1];
        $response = $this->postJson('/api/films', $json);

        $response->assertStatus(UNAUTHORIZED);
    }

    public function test_goingOverLimitOfCreateCallShouldReturnCode429()
    {
        $this->seed();

        $jsonAdmin = ["login"=>"admin",
        "password"=>"admin",
        "password_confirmation"=>"admin",
        "email"=>"admin@hotmail.com",
        "last_name"=>"Admin",
        "first_name"=>"Admin",
        "role_id"=>2];
        $this->postJson('/api/signup', $jsonAdmin);
        $admin = Auth::User();

        Sanctum::actingAs(
            $admin
        );

        $json = ["title" => "test", "release_year" => "2000", "length" => 120, "description" => "test film", "rating" => 10,
            "special_features" => "Trailers", "image" => "test film image", "language_id" => 1]; 

        for ($i = 0; $i < THROTTLING_ROUTES; $i++)
        {
            $this->post('/api/signup', $json);
        }
        
        $response = $this->post('/api/signup', $json);

        $response->assertStatus(TOO_MANY_ATTEMPTS);
    }

    public function test_adminCanUpdateFilm()
    {
        $this->seed();

        $jsonAdmin = ["login"=>"admin",
        "password"=>"admin",
        "password_confirmation"=>"admin",
        "email"=>"admin@hotmail.com",
        "last_name"=>"Admin",
        "first_name"=>"Admin",
        "role_id"=>2];
        $this->postJson('/api/signup', $jsonAdmin);
        $admin = Auth::User();

        Sanctum::actingAs(
            $admin
        );

        $json = ["title" => "test",
        "release_year" => "2000",
        "length" => 120,
        "description" => "test film",
        "rating" => 10,
        "special_features" => "Trailers",
        "image" => "test film image",
        "language_id" => 1];
        $filmToUpdate = 1;
        $response = $this->putJson('/api/films/'.$filmToUpdate, $json);

        $response->assertStatus(OK);
        $this->assertDatabaseHas('films', $json);
    }

    public function test_updatingAFilmShouldReturn422WhenMissingField()
    {
        $this->seed();

        $jsonAdmin = ["login"=>"admin",
        "password"=>"admin",
        "password_confirmation"=>"admin",
        "email"=>"admin@hotmail.com",
        "last_name"=>"Admin",
        "first_name"=>"Admin",
        "role_id"=>2];
        $this->postJson('/api/signup', $jsonAdmin);
        $admin = Auth::User();

        Sanctum::actingAs(
            $admin
        );

        $json = ["release_year" => "2000",
        "length" => 120,
        "description" => "test film",
        "rating" => 10,
        "special_features" => "Trailers",
        "image" => "test film image",
        "language_id" => 1];
        $filmToUpdate = 1;
        $response = $this->putJson('/api/films/'.$filmToUpdate, $json);

        $response->assertStatus(INVALID_DATA);
    }

    public function test_updatingNonExistingFilmShouldReturnNotFound()
    {
        $this->seed();

        $jsonAdmin = ["login"=>"admin",
        "password"=>"admin",
        "password_confirmation"=>"admin",
        "email"=>"admin@hotmail.com",
        "last_name"=>"Admin",
        "first_name"=>"Admin",
        "role_id"=>2];
        $this->postJson('/api/signup', $jsonAdmin);
        $admin = Auth::User();

        Sanctum::actingAs(
            $admin
        );

        $json = ["title"=>"test",
        "release_year" => "2000",
        "length" => 120,
        "description" => "test film",
        "rating" => 10,
        "special_features" => "Trailers",
        "image" => "test film image",
        "language_id" => 1];
        $filmToUpdate = 1000000;
        $response = $this->putJson('/api/films/'.$filmToUpdate, $json);

        $response->assertStatus(NOT_FOUND);
    }

    public function test_normalUserTryingToUpdateFilmShouldReturnForbidden()
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

        $json = ["title"=>"test",
        "release_year" => "2000",
        "length" => 120,
        "description" => "test film",
        "rating" => 10,
        "special_features" => "Trailers",
        "image" => "test film image",
        "language_id" => 1];
        $filmToUpdate = 1;
        $response = $this->putJson('/api/films/'.$filmToUpdate, $json);

        $response->assertStatus(FORBIDDEN);
    }

    public function test_tryingToUpdateFilmWithoutBeingConnectedShouldReturnUnauthorized()
    {
        $this->seed();

        $json = ["title"=>"test",
        "release_year" => "2000",
        "length" => 120,
        "description" => "test film",
        "rating" => 10,
        "special_features" => "Trailers",
        "image" => "test film image",
        "language_id" => 1];
        $filmToUpdate = 1;
        $response = $this->putJson('/api/films/'.$filmToUpdate, $json);

        $response->assertStatus(UNAUTHORIZED);
    }

    public function test_goingOverLimitOfUpdateCallShouldReturnCode429()
    {
        $this->seed();

        $jsonAdmin = ["login"=>"admin",
        "password"=>"admin",
        "password_confirmation"=>"admin",
        "email"=>"admin@hotmail.com",
        "last_name"=>"Admin",
        "first_name"=>"Admin",
        "role_id"=>2];
        $this->postJson('/api/signup', $jsonAdmin);
        $admin = Auth::User();

        Sanctum::actingAs(
            $admin
        );

        $json = ["title"=>"test",
        "release_year" => "2000",
        "length" => 120,
        "description" => "test film",
        "rating" => 10,
        "special_features" => "Trailers",
        "image" => "test film image",
        "language_id" => 1];
        $filmToUpdate = 1;

        for ($i = 0; $i < THROTTLING_ROUTES; $i++)
        {
            $this->putJson('/api/films/'.$filmToUpdate, $json);
        }
        
        $response = $this->putJson('/api/films/'.$filmToUpdate, $json);

        $response->assertStatus(TOO_MANY_ATTEMPTS);
    }

    public function test_adminCanDeleteFilm()
    {
        $this->seed();

        $jsonAdmin = ["login"=>"admin",
        "password"=>"admin",
        "password_confirmation"=>"admin",
        "email"=>"admin@hotmail.com",
        "last_name"=>"Admin",
        "first_name"=>"Admin",
        "role_id"=>2];
        $this->postJson('/api/signup', $jsonAdmin);
        $admin = Auth::User();

        Sanctum::actingAs(
            $admin
        );

        $filmToDelete = 1;
        $response = $this->delete('/api/films/'.$filmToDelete);

        $response->assertStatus(NO_CONTENT);
        $this->assertDatabaseMissing('films', ["id"=>$filmToDelete]);
    }

    public function test_deletingNonExistingFilmShouldReturnNotFound()
    {
        $this->seed();

        $jsonAdmin = ["login"=>"admin",
        "password"=>"admin",
        "password_confirmation"=>"admin",
        "email"=>"admin@hotmail.com",
        "last_name"=>"Admin",
        "first_name"=>"Admin",
        "role_id"=>2];
        $this->postJson('/api/signup', $jsonAdmin);
        $admin = Auth::User();

        Sanctum::actingAs(
            $admin
        );

        $filmToDelete = 1000000;
        $response = $this->delete('/api/films/'.$filmToDelete);

        $response->assertStatus(NOT_FOUND);
    }

    public function test_normalUserTryingToDeleteFilmShouldReturnForbidden()
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

        $filmToDelete = 1;
        $response = $this->delete('/api/films/'.$filmToDelete);

        $response->assertStatus(FORBIDDEN);
    }

    public function test_tryingToDeleteFilmWithoutBeingConnectedShouldReturnUnauthorized()
    {
        $this->seed();

        $filmToDelete = 1;
        $response = $this->deleteJson('/api/films/'.$filmToDelete);

        $response->assertStatus(UNAUTHORIZED);
    }

    public function test_goingOverLimitOfDeleteCallShouldReturnCode429()
    {
        $this->seed();

        $jsonAdmin = ["login"=>"admin",
        "password"=>"admin",
        "password_confirmation"=>"admin",
        "email"=>"admin@hotmail.com",
        "last_name"=>"Admin",
        "first_name"=>"Admin",
        "role_id"=>2];
        $this->postJson('/api/signup', $jsonAdmin);
        $admin = Auth::User();

        Sanctum::actingAs(
            $admin
        );

        for ($i = 0; $i < THROTTLING_ROUTES; $i++)
        {
            $this->delete('/api/films/'.$i);
        }
        
        $response = $this->delete('/api/films/'.$i);

        $response->assertStatus(TOO_MANY_ATTEMPTS);
    }
}
