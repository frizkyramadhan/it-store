<?php

namespace App\Models;

use App\Models\GoodIssue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function goodissues()
    {
        return $this->hasMany(GoodIssue::class);
    }
}
