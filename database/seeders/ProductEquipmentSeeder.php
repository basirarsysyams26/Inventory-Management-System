<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductEquipment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductEquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductEquipment::create([
            'location_id' => 1,
            'product_equipment_type_id' => 19,
            'sn' => 'TSALT2090',
            'tagg' => 'GA78866',
            'merk' => 'MerkABC',
        ]);        
    }
}