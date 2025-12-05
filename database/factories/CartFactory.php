<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition()
    {
        $subtotal = $this->faker->numberBetween(1000, 10000);
        $discount = $this->faker->numberBetween(0, 500);
        $total = $subtotal - $discount;

        return [
            'user_id' => User::inRandomOrder()->first()->id ?? 1,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
        ];
    }
}
