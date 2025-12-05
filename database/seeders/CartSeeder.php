<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\CartItem;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        // Generate 20 carts
        Cart::factory(20)->create()->each(function ($cart) {
            // Each cart has 1–5 items
            CartItem::factory(rand(1, 5))->create([
                'cart_id' => $cart->id,
            ]);
        });
    }
}
