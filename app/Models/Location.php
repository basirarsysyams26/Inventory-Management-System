<?php

namespace App\Models;

use App\Models\ProductEquipment;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'locations';
    protected $fillable = ['nama', 'kota', 'divisi'];

    public function productEquipments()
    {
        return $this->hasMany(ProductEquipment::class);
    }
} 