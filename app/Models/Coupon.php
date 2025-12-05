<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model {
    use HasFactory;
    protected $fillable = ['code','type','value','max_uses','used','min_cart_value','start_date','end_date','is_active'];

    public function usages() {
        return $this->hasMany(CouponUsage::class);
    }
}
