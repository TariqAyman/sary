<?php

namespace Tests;

use Exception;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations;
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:refresh');
        Artisan::call('db:seed');
    }

    public function __get($key)
    {
        if ($key === 'faker') return $this->faker;
        throw new Exception('Unknown Key Requested');
    }

    protected function authenticate()
    {
        $user = ['email' => 'admin@admin.com', 'password' => 'password'];

        return Auth::attempt($user);
    }

}
