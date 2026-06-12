<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EndUser extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function salesTargets()
    {
        return $this->hasMany(SalesTarget::class);
    }

    public function salesRealizations()
    {
        return $this->hasMany(SalesRealization::class);
    }
}
