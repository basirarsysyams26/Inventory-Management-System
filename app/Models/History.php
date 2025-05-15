<?php

namespace App\Models;

use App\Models\User;
use App\Models\Image;
use App\Models\ElementTest;
use App\Models\ProductEquipment;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'histories';

    protected $fillable = [
        'product_equipment_id', 'user_id', 'waktu_mulai', 'waktu_selesai',
        'status_akhir', 'keterangan_akhir', 'is_canceled'
    ];

    public function productEquipment()
    {
        return $this->belongsTo(ProductEquipment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function elementTests()
    {
        return $this->hasMany(ElementTest::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }
} 