<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'demo@ikman.test'],
            ['name' => 'Demo Seller', 'phone' => '0712345678', 'password' => Hash::make('password')]
        );

        $this->call([
            LocationSeeder::class,
            CategorySeeder::class,
        ]);
    }
}
