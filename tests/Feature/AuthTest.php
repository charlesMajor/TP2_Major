<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Http\Controllers\Controller;
use Auth;

class AuthTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_goingOverLimitOfSignupCallShouldReturnCode429()
    {
        $this->seed();

        Sanctum::actingAs(
            User::factory()->create(), ['*']
        );

        //Valeur hardcodée pour le throttling après communication avec le prof :D
        for ($i = 0; $i < 5; $i++)
        {
            $json = ["login"=>"test", "password"=>"test", "password_confirmation"=>"test", "email"=>"test".$i."@hotmail.com", "last_name"=> "st", "first_name"=>"Te", "role_id"=>1];
            $this->post('/api/signup', $json);
        }

        $json = ["login"=>"test", "password"=>"test", "password_confirmation"=>"test", "email"=>"test5@hotmail.com", "last_name"=> "st", "first_name"=>"Te", "role_id"=>1];
        $response = $this->post('/api/signup', $json);

        $response->assertStatus(TOO_MANY_ATTEMPTS);
    }

    public function test_canRegisterAnUser()
    {
        $this->seed();

        $json = ["login"=>"test", "password"=>"test", "password_confirmation"=>"test", "email"=>"test@hotmail.com", "last_name"=> "st", "first_name"=>"Te", "role_id"=>1];
        $jsonemail = ["email"=>"test@hotmail.com"];

        $response = $this->post('/api/signup', $json);
        
        $user = Auth::user();
        $tokens = $user->tokens;
        
        $this->assertTrue(count($tokens) == 1);
        $response->assertJsonFragment($response->json(['userToken' => $tokens[0]->plainTextToken]));
        $response->assertStatus(CREATED);
        $this->assertDatabaseHas('users', $jsonemail);
    }

    public function test_registeringWithWrongIncompleteInformationsThrowsInvalidData()
    {
        $this->seed();

        $json = ["login"=>"test", "password"=>"test", "email"=>"test@hotmail.com"];
        $response = $this->post('/api/signup', $json);

        $response->assertStatus(INVALID_DATA);
    }

    public function test_goingOverLimitOfSigninCallShouldReturnCode429()
    {
        $this->seed();

        Sanctum::actingAs(
            User::factory()->create(), ['*']
        );

        $json = ["email"=>"test@hotmail.com", "password"=>"test"];
        for ($i = 0; $i < THROTTLING_AUTH; $i++)
        {
            $this->post('/api/signin', $json);
        }

        $response = $this->post('/api/signin', $json);

        $response->assertStatus(TOO_MANY_ATTEMPTS);
    }

    public function test_canLoginAsAnUser()
    {
        $this->seed();

        $signup = ["login"=>"test", "password"=>"test", "password_confirmation"=>"test", "email"=>"test@hotmail.com", "last_name"=> "st", "first_name"=>"Te", "role_id"=>1];
        $jsonemail = ["email"=>"test@hotmail.com"];
        $this->post('/api/signup', $signup);
        $user = Auth::user();
        $tokensAtSignup = count($user->tokens);

        $json = ["email"=>"test@hotmail.com", "password"=>"test"];
        $response = $this->post('/api/signin', $json);
        
        $user = Auth::user();
        $tokens = $user->tokens;
    
        $this->assertTrue(count($tokens) == $tokensAtSignup + 1);
        $response->assertJsonFragment($response->json(['userToken' => $tokens[count($tokens) - 1]->plainTextToken]));
        $response->assertStatus(OK);
        $this->assertDatabaseHas('users', $jsonemail);
    }

    public function test_loginWithWrongInformationsThrowsInvalidData()
    {
        $this->seed();

        $signup = ["login"=>"test", "password"=>"test", "password_confirmation"=>"test", "email"=>"test@hotmail.com", "last_name"=> "st", "first_name"=>"Te", "role_id"=>1];
        $jsonemail = ["email"=>"test@hotmail.com"];
        $this->post('/api/signup', $signup);

        $json = ["login"=>"test"];
        $response = $this->post('/api/signin', $json);

        $response->assertStatus(INVALID_DATA);
    }

    public function test_loginToNonExistingUserThrowsInvalidData()
    {
        $this->seed();

        $json = ["login"=>"test", "password"=>"test"];
        $response = $this->post('/api/signin', $json);
        
        $response->assertStatus(INVALID_DATA);
    }

    public function test_goingOverLimitOfSignoutCallShouldReturnCode429()
    {
        $this->seed();

        Sanctum::actingAs(
            User::factory()->create(), ['*']
        );

        for ($i = 0; $i < THROTTLING_AUTH; $i++)
        {
            $this->post('/api/signout');
        }

        $response = $this->post('/api/signout');

        $response->assertStatus(TOO_MANY_ATTEMPTS);
    }

    public function test_canLogoutAsAnUser()
    {
        $this->seed();

        Sanctum::actingAs(
            User::factory()->create(), ['*']
        );

        $signup = ["login"=>"test", "password"=>"test", "password_confirmation"=>"test", "email"=>"test@hotmail.com", "last_name"=> "st", "first_name"=>"Te", "role_id"=>1];
        $jsonemail = ["email"=>"test@hotmail.com"];
        $this->post('/api/signup', $signup);

        $response = $this->post('/api/signout');
        
        $user = Auth::user();
        $tokens = $user->tokens;
        
        $this->assertTrue(count($tokens) == 0);
        $response->assertStatus(NO_CONTENT);
        $this->assertDatabaseHas('users', $jsonemail);
    }
}
