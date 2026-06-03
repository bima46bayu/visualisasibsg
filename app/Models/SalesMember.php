<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesMember extends Model
{
    protected $fillable = ['team_id', 'code', 'name', 'status'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function salesTargets()
    {
        return $this->hasMany(SalesTarget::class);
    }

    public function salesRealizations()
    {
        return $this->hasMany(SalesRealization::class);
    }
}
