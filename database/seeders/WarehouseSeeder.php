<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Warehouse::create([
            'bouwheer_id' => 1,
            'warehouse_name' => 'IT Spt. Room',
            'warehouse_location' => 'HO Balikpapan',
            'warehouse_type' => 'main',
            'warehouse_status' => 'active'
        ]);
    }
}
