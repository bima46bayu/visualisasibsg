<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $fillable = ['code', 'name', 'status'];

    public function salesTargets()
    {
        return $this->hasMany(SalesTarget::class);
    }

    public function salesRealizations()
    {
        return $this->hasMany(SalesRealization::class);
    }
}
