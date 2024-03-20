<?php

namespace Database\Seeders;

use App\Models\Bouwheer;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BouwheerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bouwheer::create([
            'bouwheer_name' => 'PT. Arkananta Apta Pratista',
            'alias' => 'arka',
            'bouwheer_remarks' => 'Arkananta',
            'bouwheer_status' => 'active'
        ]);
    }
}
