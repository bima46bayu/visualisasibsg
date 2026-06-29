<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $fillable = ['code', 'name', 'status'];

    public function profitabilities()
    {
        return $this->hasMany(Profitability::class);
    }
    
    public function subEntities()
    {
        return $this->hasMany(ProfitabilitySubEntity::class, 'entity_id');
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
