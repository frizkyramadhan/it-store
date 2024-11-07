<?php

namespace App\Models;

use App\Models\User;
use App\Models\TrfDetail;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transfer extends Model
{
    use HasFactory, HasUlids;

    protected $guarded = [];

    public function trfdetails()
    {
        return $this->hasMany(TrfDetail::class);
    }

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'trf_from', 'id');
    }

    // Relasi ke warehouse tujuan (trf_to)
    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'trf_to', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
