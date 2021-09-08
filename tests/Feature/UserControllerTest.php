<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function testGetAllUsers()
    {
        User::factory()->count(5)->make();

        $users = User::query()->get();

        foreach ($users as $user) {
            $data[] = [
                'id' => (int) $user->id,
                'name' => (string) $user->name,
                'email' => (string) $user->email,
                'employee_number' => (int) $user->employee_number,
                'user_type' => (string) $user->user_type,
                'isAdmin' => (bool) $user->isAdmin,
                'isEmployee' => (bool) $user->isEmployee,
            ];
        }

        $token = $this->authenticate();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])
            ->json('GET', 'api/users')
            ->assertStatus(200)
            ->assertJson([
                'data' => $data
            ]);
    }

    public function testGetUserInfo()
    {
        $user = User::query()->first();

        $token = $this->authenticate();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])
            ->json('GET', "api/users/{$user->id}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => (int) $user->id,
                    'name' => (string) $user->name,
                    'email' => (string) $user->email,
                    'employee_number' => (int) $user->employee_number,
                    'user_type' => (string) $user->user_type,
                    'isAdmin' => (bool) $user->isAdmin,
                    'isEmployee' => (bool) $user->isEmployee,
                ]
            ]);

    }

    public function testDeleteUser()
    {
        $user = User::query()->first();

        $token = $this->authenticate();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])
            ->json('DELETE', "api/users/{$user->id}")
            ->assertStatus(200);
    }

    public function testUpdateUserInfo()
    {
        $user = User::query()->first();

        $token = $this->authenticate();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])
            ->json('PUT', "api/users/{$user->id}", [
                'name' => 'new name',
                'email' => 'new@email.com',
                'employee_number' =>  $user->employee_number,
                'user_type' => $user->user_type,
            ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => (int) $user->id,
                    'name' => 'new name',
                    'email' => 'new@email.com',
                    'employee_number' => (int) $user->employee_number,
                    'user_type' => (string) $user->user_type,
                    'isAdmin' => (bool) $user->isAdmin,
                    'isEmployee' => (bool) $user->isEmployee,
                ]
            ]);
    }

    public function testUserCreateWithoutApiToken()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ',])
            ->json('POST', 'api/users', [
                'name' => 'Test Name',
                'email' => "admin2@admin.com",
                'password' => 'password',
                'employee_number' => $this->faker->unique()->numberBetween(1111,9999),
                'user_type' => 'employee'
            ]);
        $response->assertStatus(401)
            ->assertJson([
                'error' => "You are Unauthorized."
            ]);
    }

    public function testUserCreateSuccessfully()
    {
        $token = $this->authenticate();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])
            ->json('POST', 'api/users', [
                'name' => 'Test Name',
                'email' => "admin2@admin.com",
                'password' => 'password',
                'employee_number' => 4444,
                'user_type' => 'employee'
            ]);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'name',
                    'email',
                    'employee_number',
                    'user_type',
                    'id',
                    'isAdmin',
                    'isEmployee',
                    'updated_at',
                    'created_at'
                ]
            ]);
    }

//    public function testUserCreateWithErrorRequired()
//    {
//        $token = $this->authenticate();
//
//        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])
//            ->json('POST', 'api/users', []);
//        $response->assertStatus(200)
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
}
