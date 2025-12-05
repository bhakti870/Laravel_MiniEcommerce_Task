<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model {
    protected $fillable = ['order_id','product_sku_id','quantity','price','discount','total'];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function productSku() {
        return $this->belongsTo(ProductSku::class);
    }
}

