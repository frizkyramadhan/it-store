<?php

namespace App\Models;

use App\Models\Item;
use App\Models\GoodReceive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GrDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function goodreceive()
    {
        return $this->belongsTo(GoodReceive::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
