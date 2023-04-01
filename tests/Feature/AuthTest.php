<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use WithFaker;

    public function testRequiredFieldsForRegistration()
    {
        $this->json('POST', route('api.register'), ['Accept' => 'application/json', 
        'Content-Type' => 'application/json'])
            ->assertStatus(400);
    }

    public function testSuccessfulLogin()
    {
        $email = $this->faker->unique()->safeEmail;
        $user = User::create([
            'name' => $this->faker->name,
           'email' => $email,
           'password' => bcrypt('sample123'),
        ]);


        $loginData = ['email' => $email, 'password' => 'sample123'];

        $this->postJson(route('api.login'), $loginData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
               "data" => [
                "user" => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ],
                 "token"
               ]
            ]);

        $this->assertAuthenticated();
    }


    public function testFailedRegisteration()
    {
        $response = $this->get(route('api.register'), [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password',
            'password_confirmation' => 'invalid_password'
        ]);
    
        $response->assertStatus(400);
    }

    public function testSuccessfulRegisteration()
    {
        $email = $this->faker->unique()->safeEmail;
        $response = $this->post(route('api.register'), [
            'name' => 'John Doe',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users',[
            'email' => $email,
        ]);
    }

    public function testSuccessfulLogout(){
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('api.logout'));

        $response->assertStatus(200);
    }


    public function testFailedLogout(){
        $response = $this->postjson(route('api.logout'));

        $response->assertStatus(401);
    }
}
