<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Vendor::create([
            'vendor_name' => 'Century',
            'vendor_address' => 'Komp. Balikpapan Baru Blok AB 4 No 8',
            'vendor_phone' => '+62 813 518 04800',
            'vendor_status' => 'active'
        ]);
    }
}
