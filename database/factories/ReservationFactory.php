<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReservationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Reservation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $table = Table::query()->inRandomOrder()->first();

        return [
            'table_id' => $table->id,
            'start_date' => $startDate = Carbon::now()->setTime(12, 30)->addDays($this->faker->numberBetween(1, 30)),
            'end_date' => $startDate->copy()->addHours(2),
            'customer_seat' => $table->seats,
        ];
    }

}
