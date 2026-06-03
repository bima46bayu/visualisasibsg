<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['code', 'name', 'status'];

    public function salesMembers()
    {
        return $this->hasMany(SalesMember::class);
    }
}
