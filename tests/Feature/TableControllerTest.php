<?php

namespace Tests\Feature;

use App\Models\Table;
use Facade\Ignition\Tabs\Tab;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TableControllerTest extends TestCase
{
    public function testGetAllTables()
    {
        Table::factory()->count(5)->make();

        $tables = Table::query()->get();

        foreach ($tables as $table) {
            $data[] = [
                'id' => (int) $table->id,
                'number' => (int) $table->number,
                'seats' => (int) $table->seats,
            ];
        }

        $token = $this->authenticate();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])
            ->json('GET', 'api/tables')
            ->assertStatus(200)
            ->assertJson([
                'data' => $data
            ]);
    }

    public function testGetTableInfo()
    {
        $table = Table::query()->first();

        $token = $this->authenticate();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])
            ->json('GET', "api/tables/{$table->id}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => (int) $table->id,
                    'number' => (int) $table->number,
                    'seats' => (int) $table->seats,
                ]
            ]);

    }

    public function testDeleteTable()
    {
        $table = Table::query()->first();

        $token = $this->authenticate();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])
            ->json('DELETE', "api/tables/{$table->id}")
            ->assertStatus(200);
    }

    public function testUpdateTableInfo()
    {
        $table = Table::query()->first();

        $token = $this->authenticate();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])
            ->json('PUT', "api/tables/{$table->id}", [
                'seats' => (int) 11,
            ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => (int) $table->id,
                    'number' => (int) $table->number,
                    'seats' => (int) 11,
                ]
            ]);
    }

    public function testTableCreateWithoutApiToken()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ',])
            ->json('POST', 'api/tables', [
                'number' => (int) 100,
                'seats' => (int) 11,
            ]);
        $response->assertStatus(401)
            ->assertJson([
                'error' => "You are Unauthorized."
            ]);
    }

    public function testTableCreateSuccessfully()
    {
        $token = $this->authenticate();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])
            ->json('POST', 'api/tables', [
                'number' => (int) 100,
                'seats' => (int) 11,
            ]);
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'number' => (int) 100,
                    'seats' => (int) 11,
                ]
            ]);
    }

//    public function testTableCreateWithErrorRequired()
//    {
//        $token = $this->authenticate();
//
//        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])
//            ->json('POST', 'api/tables', []);
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
