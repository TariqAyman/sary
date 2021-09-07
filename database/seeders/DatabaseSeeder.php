<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // create first admin :
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'user_type' => 'admin'
        ]);

        \App\Models\User::factory(10)->create();

        \App\Models\Table::factory(10)->create();

    }
}
