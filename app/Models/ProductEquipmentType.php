<?php

namespace App\Models;

use App\Models\ProductEquipment;
use App\Models\MasterElementTest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductEquipmentType extends Model
{
    protected $table = 'product_equipment_types';
    protected $fillable = ['name'];

    // Relasi ke product_equipments
    public function productEquipments(): HasMany
    {
        return $this->hasMany(ProductEquipment::class);
    }

    // Relasi ke master_element_tests
    public function masterElementTests(): HasMany
    {
        return $this->hasMany(MasterElementTest::class);
    }
}