<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use Auth;


class UserTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */

     public function test_userCanGetHisInfos()
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

        $userId = $norm->id;
        $response = $this->get('/api/users/'.$userId);
        $jsonResponse = ["login"=>"norm", "email"=>"notadmin@hotmail.com", "last_name"=>"Admin", "first_name"=>"Not", "role_id"=>1];

        $response->assertJsonFragment($jsonResponse);
        $response->assertStatus(OK);
    }

    public function test_userTryingToGetAnotherUsersInfosShouldReturnForbidden()
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

        $userId = 100;
        $response = $this->get('/api/users/'.$userId);

        $response->assertStatus(FORBIDDEN);
    }

    public function test_userTryingToGetInfosWithoutBeingConnectedShouldReturnUnauthorized()
    {
        $this->seed();

        $userId = 1;
        $response = $this->getJson('/api/users/'.$userId);

        $response->assertStatus(UNAUTHORIZED);
    }

    public function test_goingOverLimitOfGetCallShouldReturnCode429()
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

        $userId = $norm->id;
        for ($i = 0; $i < THROTTLING_ROUTES; $i++)
        {
            $this->get('/api/users/'.$userId);
        }
        
        $response = $this->get('/api/users/'.$userId);

        $response->assertStatus(TOO_MANY_ATTEMPTS);
    }

    public function test_userCanEditHisPassword()
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

        $json = ["password" => "test",
        "password_confirmation" => "test"];
        $userId = $norm->id;
        $response = $this->patchJson('/api/users/'.$userId, $json);

        $response->assertStatus(OK);
    }

    public function test_editingPasswordShouldReturn422WhenMissingField()
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

        $json = ["password" => "test"];
        $userId = $norm->id;
        $response = $this->patchJson('/api/users/'.$userId, $json);

        $response->assertStatus(INVALID_DATA);
    }

    public function test_editingPasswordShouldReturn422WhenPasswordConfirmationIsNotTheSame()
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

        $json = ["password" => "test",
        "password_confirmation" => "testDifferent"];
        $userId = $norm->id;
        $response = $this->patchJson('/api/users/'.$userId, $json);

        $response->assertStatus(INVALID_DATA);
    }

    public function test_userTryingToEditAnotherUsersPasswordShouldReturnForbidden()
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

        $json = ["password" => "test",
        "password_confirmation" => "test"];
        $userId = 10000;
        $response = $this->patchJson('/api/users/'.$userId, $json);

        $response->assertStatus(FORBIDDEN);
    }

    public function test_tryingToEditPasswordWithoutBeingConnectedShouldReturnUnauthorized()
    {
        $this->seed();

        $json = ["password" => "test",
        "password_confirmation" => "test"];
        $userId = 1;
        $response = $this->patchJson('/api/users/'.$userId, $json);

        $response->assertStatus(UNAUTHORIZED);
    }

    public function test_goingOverLimitOfEditCallShouldReturnCode429()
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

        $json = ["password" => "test",
        "password_confirmation" => "test"];
        $userId = $norm->id;
        for ($i = 0; $i < THROTTLING_ROUTES; $i++)
        {
            $this->patchJson('/api/users/'.$userId, $json);
        }
        
        $response = $this->patchJson('/api/users/'.$userId, $json);

        $response->assertStatus(TOO_MANY_ATTEMPTS);
    }
}
