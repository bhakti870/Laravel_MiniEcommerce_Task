<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model {
    protected $fillable = ['coupon_id','user_id','discount'];

    public function coupon() {
        return $this->belongsTo(Coupon::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}

