<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfitabilityItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function profitability()
    {
        return $this->belongsTo(Profitability::class);
    }
}
