<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Course;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = Category::factory()->create();
        $subcategory = SubCategory::factory()->create(['category_id' => $category->id]);
        
        return [
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'instructor_id' => User::factory(),
            'course_image' => null,
            'course_title' => fake()->sentence(),
            'course_name' => fake()->sentence(),
            'course_name_slug' => fake()->slug(),
            'description' => fake()->paragraph(),
            'video_url' => null,
            'label' => null,
            'resources' => null,
            'certificate' => null,
            'duration' => fake()->randomFloat(2, 1, 100),
            'selling_price' => fake()->numberBetween(0, 1000),
            'discount_price' => null,
            'prerequisites' => null,
            'bestseller' => null,
            'featured' => null,
            'highestrated' => null,
            'status' => 1, // Active
        ];
    }
}

