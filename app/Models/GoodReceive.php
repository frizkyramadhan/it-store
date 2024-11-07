<?php

namespace App\Models;

use App\Models\User;
use App\Models\Vendor;
use App\Models\GrDetail;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoodReceive extends Model
{
    use HasFactory, HasUlids;

    protected $guarded = [];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function grdetails()
    {
        return $this->hasMany(GrDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
