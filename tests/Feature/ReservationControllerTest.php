<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReservationControllerTest extends TestCase
{
    public function testGetAllReservations()
    {
        Reservation::factory()->count(20)->make();

        $reservations = Reservation::query()->limit(15)->get();

        foreach ($reservations as $reservation) {
            $data[] = [
                'id' => (int) $reservation->id,
                'table_id' => (int) $reservation->table_id,
                'start_date' => (string) $reservation->start_date,
                'end_date' => (string) $reservation->end_date,
                'customer_seat' => (int) $reservation->customer_seat,
                'table' => [
                    "id" => (int) $reservation->table->id,
                    "number" => (int) $reservation->table->number,
                    "seats" => (int) $reservation->table->seats,
                ],
            ];
        }

        $token = $this->authenticate();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])
            ->json('GET', 'api/reservations')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            "id",
                            "table_id",
                            "start_date",
                            "end_date",
                            "customer_seat",
                            "created_at",
                            "updated_at",
                            "table" => [
                                "id",
                                "number",
                                "seats",
                            ]
                        ],
                    ],
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links' => [
                        [
                            "url",
                            "label",
                            "active",
                        ],
                        [
                            "url",
                            "label",
                            "active",
                        ],
                        [
                            "url",
                            "label",
                            "active",
                        ]
                    ],
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    "to",
                    "total"
                ],
            ]);
    }

    public function testGetTodayReservations()
    {
        Reservation::factory()->count(20)->make();

        $reservations = Reservation::query()
            ->whereDate('start_date', '<=', Carbon::now()->endOfDay())
            ->limit(15)->get();

        foreach ($reservations as $reservation) {
            $data[] = [
                'id' => (int) $reservation->id,
                'table_id' => (int) $reservation->table_id,
                'start_date' => (string) $reservation->start_date,
                'end_date' => (string) $reservation->end_date,
                'customer_seat' => (int) $reservation->customer_seat,
                'table' => [
                    "id" => (int) $reservation->table->id,
                    "number" => (int) $reservation->table->number,
                    "seats" => (int) $reservation->table->seats,
                ],
            ];
        }

        $token = $this->authenticate();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])
            ->json('GET', 'api/reservations/today')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            "id",
                            "table_id",
                            "start_date",
                            "end_date",
                            "customer_seat",
                            "created_at",
                            "updated_at",
                            "table" => [
                                "id",
                                "number",
                                "seats",
                            ]
                        ],
                    ],
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links' => [
                        [
                            "url",
                            "label",
                            "active",
                        ],
                        [
                            "url",
                            "label",
                            "active",
                        ],
                        [
                            "url",
                            "label",
                            "active",
                        ]
                    ],
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    "to",
                    "total"
                ],
            ]);
    }

    public function testGetReservationInfo()
    {
        $reservation = Reservation::query()->first();

        $token = $this->authenticate();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])
            ->json('GET', "api/reservations/{$reservation->id}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    "id" => (int) $reservation->id,
                    "table_id" => (int) $reservation->table_id,
                    "start_date" =>  $reservation->start_date->toDateTime(),
                    "end_date" =>  $reservation->end_date->toDateTime(),
                    "customer_seat" => (int) $reservation->customer_seat,
                    "created_at" => (string) $reservation->created_at,
                    "updated_at" => (string) $reservation->updated_at,
                    "table" => [
                        "id" => (int) $reservation->table->id,
                        "number" => (int) $reservation->table->number,
                        "seats" => (int) $reservation->table->seats,
                    ]
                ]
            ]);

    }

//    public function testDeleteReservation()
//    {
//        $reservation = Reservation::query()->first();
//
//        $token = $this->authenticate();
//
//        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])
//            ->json('DELETE', "api/reservations/$reservation->id}")
//            ->assertStatus(200);
//    }

    public function testUpdateReservationInfo()
    {
        $reservation = Reservation::query()->first();

        $token = $this->authenticate();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])
            ->json('PUT', "api/reservations/{$reservation->id}", [
                'customer_seat' => (int) 11,
            ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    "id" => (int) $reservation->id,
                    "table_id" => (int) $reservation->table_id,
                    "start_date" =>  $reservation->start_date,
                    "end_date" =>  $reservation->end_date,
                    "customer_seat" => (int) 11,
                    "created_at" => (string) $reservation->created_at,
                    "updated_at" => (string) $reservation->updated_at,
                    "table" => [
                        "id" => (int) $reservation->table->id,
                        "number" => (int) $reservation->table->number,
                        "seats" => (int) $reservation->table->seats,
                    ]
                ]
            ]);
    }

    public function testReservationCreateWithoutApiToken()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ',])
            ->json('POST', 'api/reservations', [
            ]);
        $response->assertStatus(401)
            ->assertJson([
                'error' => "You are Unauthorized."
            ]);
    }

    public function testReservationCreateSuccessfully()
    {
        $token = $this->authenticate();

        $table = Table::query()->inRandomOrder()->first();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])
            ->json('POST', 'api/reservations', [
                "table_id" => $table->id,
                "start_date" => $endDate = Carbon::now()->setTime(13, 0),
                "end_date" => $endDate->copy()->addHours(2),
                "customer_seat" => (int) $table->seats,
            ]);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    "id",
                    "table_id",
                    "start_date",
                    "end_date",
                    "customer_seat",
                    "created_at",
                    "updated_at",
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
