<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BrandFactory extends Factory
{
    public function definition()
    {
        $name = $this->faker->unique()->company();

        return [
            'name'   => $name,
            'slug'   => Str::slug($name),
            'logo'   => 'default-logo.png',  // or use faker image: $this->faker->imageUrl()
            'status' => rand(0, 1),
        ];
    }
}
