<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CustomerCategory;

class Customer extends Model
{
    //
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(CustomerCategory::class, 'customer_category_id');
    }

    public function district()
    {
        return $this->belongsTo(\Laravolt\Indonesia\Models\District::class);
    }
}
