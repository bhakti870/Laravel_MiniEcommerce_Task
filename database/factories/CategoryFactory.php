<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition()
    {
        return [
            'name'      => $this->faker->unique()->words(2, true),
            'status'    => rand(0, 1),
            'parent_id' => null, // will update in seeder for child categories
        ];
    }
}
