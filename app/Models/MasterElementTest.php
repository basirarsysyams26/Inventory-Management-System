<?php

namespace App\Models;

use App\Models\ProductEquipmentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterElementTest extends Model
{
    protected $table = 'master_element_tests';
    protected $fillable = [
        'product_equipment_type_id',
        'nama_element',
        'parameter',
        'keterangan_ok',
        'keterangan_not_ok'
    ];

    // Relasi ke product_equipment_type
    public function productEquipmentType(): BelongsTo
    {
        return $this->belongsTo(ProductEquipmentType::class);
    }
}