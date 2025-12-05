<?php

namespace Database\Factories;

use App\Models\CartItem;
use App\Models\ProductSku;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition()
    {
        $price = $this->faker->numberBetween(100, 1000);
        $quantity = $this->faker->numberBetween(1, 5);
        $discount = $this->faker->numberBetween(0, 50);
        $total = ($price * $quantity) - $discount;

        return [
            'product_sku_id' => ProductSku::inRandomOrder()->first()->id ?? 1,
            'quantity' => $quantity,
            'price' => $price,
            'discount' => $discount,
            'total' => $total,
        ];
    }
}
