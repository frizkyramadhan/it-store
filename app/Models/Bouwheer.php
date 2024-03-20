<?php

namespace App\Models;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bouwheer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }
}
