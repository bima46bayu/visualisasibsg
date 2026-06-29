<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfitabilitySubEntity extends Model
{
    use HasFactory;

    protected $fillable = ['entity_id', 'name', 'code', 'is_active'];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }
    
    public function profitabilities()
    {
        return $this->hasMany(Profitability::class, 'sub_entity_id');
    }
}
