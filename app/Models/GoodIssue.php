<?php

namespace App\Models;

use App\Models\GiDetail;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoodIssue extends Model
{
    use HasFactory, HasUlids;

    protected $guarded = [];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function gidetails()
    {
        return $this->hasMany(GiDetail::class);
    }
}
