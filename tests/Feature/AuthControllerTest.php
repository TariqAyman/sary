<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tymon\JWTAuth\JWTAuth;

class AuthControllerTest extends TestCase
{
//    public function testRequireEmailAndPassword()
//    {
//        $this->json('POST', 'api/auth/login')
//            ->assertStatus(400)
//            ->assertJsonStructure([
//                'errors' =>
//                    [
//                        '*' => [
//                            'key',
//                            'message',
//                        ]
//                    ]
//            ]);
//    }

    public function testUserLoginSuccessFailed()
    {
        $user = ['email' => 'admin1@admin.com', 'password' => 'password'];
        $this->json('POST', 'api/auth/login', $user)
            ->assertStatus(422)
            ->assertJson([
                'error' => 'Unauthorized'
            ]);
    }

    public function testUserLoginSuccessfully()
    {
        $user = ['email' => 'admin@admin.com', 'password' => 'password'];
        $this->json('POST', 'api/auth/login', $user)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                ]
            ]);
    }

    public function testLogoutSuccessfully()
    {
        $user = ['email' => 'admin@admin.com', 'password' => 'password'];

        $token = Auth::attempt($user);

        $headers = ['Authorization' => "Bearer $token"];

        $this->json('POST', 'api/auth/logout', [], $headers)
            ->assertStatus(200);
    }

    public function testLogoutSuccessFailed()
    {
        $user = ['email' => 'admin1@admin.com', 'password' => 'password'];

        $token = Auth::attempt($user);

        $headers = ['Authorization' => "Bearer $token"];

        $this->json('POST', 'api/auth/logout', [], $headers)
            ->assertStatus(401);
    }

}
