<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductEquipmentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductEquipmentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductEquipmentType::create([
            'name' => 'ADAPTOR MODEM X112'
        ]);
      
    }
}