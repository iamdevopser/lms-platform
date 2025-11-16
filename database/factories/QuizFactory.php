<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quiz>
 */
class QuizFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'type' => fake()->randomElement(['quiz', 'exam', 'assignment']),
            'time_limit' => fake()->optional()->numberBetween(15, 120),
            'passing_score' => fake()->numberBetween(60, 85),
            'max_attempts' => fake()->numberBetween(1, 5),
            'shuffle_questions' => fake()->boolean(),
            'show_correct_answers' => fake()->boolean(),
            'show_results_immediately' => fake()->boolean(),
            'is_active' => true,
            'start_date' => fake()->optional()->dateTimeBetween('-30 days', 'now'),
            'end_date' => fake()->optional()->dateTimeBetween('now', '+90 days'),
        ];
    }
}

