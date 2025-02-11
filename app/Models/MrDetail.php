<?php

namespace App\Models;

use App\Models\Item;
use App\Models\MaterialRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MrDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function materialrequest()
    {
        return $this->belongsTo(MaterialRequest::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
