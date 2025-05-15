<?php

namespace App\Models;

use App\Models\History;
use App\Models\Location;
use App\Models\ProductEquipmentType;
use Illuminate\Database\Eloquent\Model;

class ProductEquipment extends Model
{
    protected $table = 'product_equipments';
    protected $fillable = ['product_equipment_type_id', 'location_id', 'sn', 'tagg', 'merk'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function productEquipmentType()
    {
        return $this->belongsTo(ProductEquipmentType::class);
    }

    public function historys()
    {
        return $this->hasMany(History::class);
    }
}