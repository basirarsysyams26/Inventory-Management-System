<?php

namespace App\Models;

use App\Models\History;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'images';
    protected $fillable = ['history_id', 'path'];

    public function history()
    {
        return $this->belongsTo(History::class);
    }
} 