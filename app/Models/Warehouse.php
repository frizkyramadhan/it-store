<?php

namespace App\Models;

use App\Models\Bouwheer;
use App\Models\Transfer;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function bouwheer()
    {
        return $this->belongsTo(Bouwheer::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }
}
