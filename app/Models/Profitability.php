<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profitability extends Model
{


    protected $guarded = ['id'];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function items()
    {
        return $this->hasMany(ProfitabilityItem::class);
    }
}
