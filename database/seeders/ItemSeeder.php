<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Item::create([
            'item_code' => 'PC-INTCI5GEN6',
            'description' => 'Intel Core I5 GEN 6',
            'group_id' => '1',
            'item_status' => 'active'
        ]);

        Item::create([
            'item_code' => 'RA-DDR5VGEN4GB',
            'description' => 'RAM DDR 5 V-Gen 4GB 2400MHz',
            'group_id' => '2',
            'item_status' => 'active'
        ]);

        Item::create([
            'item_code' => 'SD-SAMEVO500GB',
            'description' => 'SSD Samsung EVO 370 500GB M.2 NVMe',
            'group_id' => '3',
            'item_status' => 'active'
        ]);

        Item::create([
            'item_code' => 'MN-LEN19INC',
            'description' => 'Monitor LENOVO 19" 1920x1080',
            'group_id' => '4',
            'item_status' => 'active'
        ]);

        Item::create([
            'item_code' => 'UP-ICACT1082B',
            'description' => 'UPS ICA CT 1082B 2000VA',
            'group_id' => '5',
            'item_status' => 'active'
        ]);
    }
}
