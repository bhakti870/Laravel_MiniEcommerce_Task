<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CouponFactory extends Factory
{
    protected $model = \App\Models\Coupon::class;

    public function definition()
    {
        $types = ['fixed', 'percent'];

        return [
            'code' => strtoupper(Str::random(8)),
            'type' => $this->faker->randomElement($types),
            'value' => $this->faker->randomFloat(2, 10, 100), // 10-100 fixed or percent
            'max_uses' => $this->faker->numberBetween(10, 100),
            'used' => $this->faker->numberBetween(0, 10),
            'min_cart_value' => $this->faker->randomFloat(2, 100, 1000),
            'start_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+2 months'),
            'is_active' => $this->faker->boolean(80), // 80% chance active
        ];
    }
}
