<?php

namespace App\Models;

use App\Models\User;
use App\Models\Project;
use App\Models\MrDetail;
use App\Models\Warehouse;
use App\Models\IssuePurpose;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaterialRequest extends Model
{
    use HasFactory, HasUlids;

    protected $guarded = [];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mrdetails()
    {
        return $this->hasMany(MrDetail::class);
    }

    public function issuepurpose()
    {
        return $this->belongsTo(IssuePurpose::class, 'issue_purpose_id', 'id');
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }
}
