<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Create 10 parent categories
        $parents = Category::factory()->count(10)->create();

        // Create 10 child categories and assign random parent
        Category::factory()->count(10)->make()->each(function ($category) use ($parents) {
            $category->parent_id = $parents->random()->id;
            $category->save();
        });
    }
}
