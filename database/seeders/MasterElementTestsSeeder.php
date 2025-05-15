<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterElementTest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MasterElementTestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterElementTest::create([
            'product_equipment_type_id' => 19,
            'nama_element' => 'Tegangan Keluaran',
            'parameter' => '12V ± 5%',
            'keterangan_ok' => 'Tegangan keluaran stabil di sekitar 12 volt, toleransi ±5%',
            'keterangan_not_ok' => 'Tegangan di luar rentang 11.4V–12.6V, dapat merusak perangkat'
        ]);
        
        MasterElementTest::create([
            'product_equipment_type_id' => 19,
            'nama_element' => 'Arus Maksimum',
            'parameter' => '≥ 1A',
            'keterangan_ok' => 'Mampu menyuplai arus minimal 1 ampere sesuai spesifikasi modem',
            'keterangan_not_ok' => 'Arus kurang dari 1A, tidak mencukupi kebutuhan modem'
        ]);
        
        MasterElementTest::create([
            'product_equipment_type_id' => 19,
            'nama_element' => 'Stabilitas Output',
            'parameter' => 'Tidak fluktuatif > ±0.3V dalam 30 detik',
            'keterangan_ok' => 'Tegangan output stabil tanpa fluktuasi lebih dari 0.3 volt',
            'keterangan_not_ok' => 'Fluktuasi melebihi 0.3 volt, berpotensi gangguan fungsi modem'
        ]);
      

    }
}