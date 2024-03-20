<?php

namespace App\Models;

use App\Models\Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GiDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function goodissue()
    {
        return $this->belongsTo(GoodIssue::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
