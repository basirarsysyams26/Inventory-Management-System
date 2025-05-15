<?php

namespace App\Models;

use App\Models\History;
use Illuminate\Database\Eloquent\Model;

class ElementTest extends Model
{
    protected $table = 'element_tests';
    protected $fillable = ['history_id', 'nama_element', 'hasil_test', 'status', 'keterangan'];

    public function history()
    {
        return $this->belongsTo(History::class);
    }   
} 