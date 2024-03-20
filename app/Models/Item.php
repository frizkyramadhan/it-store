<?php

namespace App\Models;

use App\Models\Group;
use App\Models\Trfdetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory, HasUlids;

    protected $guarded = [];


    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function grdetails()
    {
        return $this->hasMany(Grdetail::class);
    }

    public function gidetails()
    {
        return $this->hasMany(Gidetail::class);
    }

    public function trfdetails()
    {
        return $this->hasMany(Trfdetail::class);
    }
}
