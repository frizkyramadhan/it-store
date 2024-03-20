<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@it-store.dev',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'status' => 'active'
        ]);

        $this->call(BouwheerSeeder::class);
        $this->call(WarehouseSeeder::class);
        $this->call(GroupSeeder::class);
        $this->call(VendorSeeder::class);
        $this->call(ItemSeeder::class);
    }
}
