<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Order extends Model {
    protected $fillable = ['user_id','subtotal','discount','total','status','snapshot'];

    protected $casts = ['snapshot'=>'array'];

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
