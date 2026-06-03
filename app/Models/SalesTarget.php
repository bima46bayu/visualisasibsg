<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesTarget extends Model
{
    protected $fillable = ['year', 'month', 'sales_member_id', 'entity_id', 'target_amount'];

    public function salesMember()
    {
        return $this->belongsTo(SalesMember::class);
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }
}
