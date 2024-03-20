<?php

namespace App\Models;

use App\Models\GoodReceive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function goodreceives()
    {
        return $this->hasMany(GoodReceive::class);
    }
}
