<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class QuizTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $instructor;
    protected $student;
    protected $course;
    protected $quiz;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create instructor
        $this->instructor = User::factory()->create([
            'role' => 'instructor'
        ]);
        
        // Create student
        $this->student = User::factory()->create([
            'role' => 'user'
        ]);
        
        // Create course
        $this->course = Course::factory()->create([
            'instructor_id' => $this->instructor->id,
            'status' => 'published'
        ]);
        
        // Create quiz
        $this->quiz = Quiz::factory()->create([
            'course_id' => $this->course->id,
            'is_active' => true
        ]);
    }

    /** @test */
    public function instructor_can_create_quiz()
    {
        $this->actingAs($this->instructor);
        
        $quizData = [
            'course_id' => $this->course->id,
            'title' => 'Test Quiz',
            'description' => 'This is a test quiz',
            'type' => 'quiz',
            'time_limit' => 30,
            'passing_score' => 70,
            'max_attempts' => 3,
            'shuffle_questions' => true,
            'show_correct_answers' => true,
            'show_results_immediately' => true,
        ];
        
        $response = $this->post(route('instructor.quizzes.store'), $quizData);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('quizzes', [
            'title' => 'Test Quiz',
            'course_id' => $this->course->id,
            'instructor_id' => $this->instructor->id,
        ]);
    }

    /** @test */
    public function instructor_can_update_quiz()
    {
        $this->actingAs($this->instructor);
        
        $updateData = [
            'course_id' => $this->course->id,
            'title' => 'Updated Quiz Title',
            'description' => 'Updated description',
            'type' => 'exam',
            'passing_score' => 80,
            'max_attempts' => 5,
        ];
        
        $response = $this->put(route('instructor.quizzes.update', $this->quiz->id), $updateData);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('quizzes', [
            'id' => $this->quiz->id,
            'title' => 'Updated Quiz Title',
            'passing_score' => 80,
        ]);
    }

    /** @test */
    public function instructor_can_delete_quiz()
    {
        $this->actingAs($this->instructor);
        
        $response = $this->delete(route('instructor.quizzes.destroy', $this->quiz->id));
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('quizzes', [
            'id' => $this->quiz->id
        ]);
    }

    /** @test */
    public function instructor_cannot_delete_quiz_with_attempts()
    {
        $this->actingAs($this->instructor);
        
        // Create a quiz attempt
        QuizAttempt::factory()->create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->student->id,
            'status' => 'completed'
        ]);
        
        $response = $this->delete(route('instructor.quizzes.destroy', $this->quiz->id));
        
        $response->assertRedirect();
        $this->assertDatabaseHas('quizzes', [
            'id' => $this->quiz->id
        ]);
    }

    /** @test */
    public function instructor_can_toggle_quiz_status()
    {
        $this->actingAs($this->instructor);
        
        $response = $this->post(route('instructor.quizzes.toggle-status', $this->quiz->id));
        
        $response->assertRedirect();
        $this->assertDatabaseHas('quizzes', [
            'id' => $this->quiz->id,
            'is_active' => false
        ]);
    }

    /** @test */
    public function instructor_can_add_questions_to_quiz()
    {
        $this->actingAs($this->instructor);
        
        $questionData = [
            'question' => 'What is 2 + 2?',
            'type' => 'single_choice',
            'options' => [
                'A' => '3',
                'B' => '4',
                'C' => '5',
                'D' => '6'
            ],
            'correct_answers' => ['B'],
            'points' => 5,
            'order' => 1,
        ];
        
        $response = $this->post(route('instructor.quizzes.questions.store', $this->quiz->id), $questionData);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id' => $this->quiz->id,
            'question' => 'What is 2 + 2?',
            'type' => 'single_choice',
        ]);
    }

    /** @test */
    public function student_can_view_available_quizzes()
    {
        $this->actingAs($this->student);
        
        $response = $this->get(route('student.quizzes.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('frontend.quiz.index');
    }

    /** @test */
    public function student_can_view_quiz_details()
    {
        $this->actingAs($this->student);
        
        $response = $this->get(route('student.quizzes.show', $this->quiz->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('frontend.quiz.show');
    }

    /** @test */
    public function student_can_start_quiz()
    {
        $this->actingAs($this->student);
        
        $response = $this->get(route('student.quizzes.start', $this->quiz->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('frontend.quiz.take');
        
        $this->assertDatabaseHas('quiz_attempts', [
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->student->id,
            'status' => 'in_progress'
        ]);
    }

    /** @test */
    public function student_can_submit_quiz()
    {
        $this->actingAs($this->student);
        
        // Create quiz attempt
        $attempt = QuizAttempt::factory()->create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->student->id,
            'status' => 'in_progress'
        ]);
        
        // Create questions
        $question = QuizQuestion::factory()->create([
            'quiz_id' => $this->quiz->id,
            'type' => 'single_choice',
            'options' => ['A' => 'Option A', 'B' => 'Option B'],
            'correct_answers' => ['A'],
            'points' => 5
        ]);
        
        $answers = [
            $question->id => 'A'
        ];
        
        $response = $this->post(route('student.quizzes.submit', $attempt->id), [
            'answers' => $answers
        ]);
        
        $response->assertRedirect(route('student.quizzes.result', $attempt->id));
        
        $this->assertDatabaseHas('quiz_answers', [
            'quiz_attempt_id' => $attempt->id,
            'quiz_question_id' => $question->id,
        ]);
        
        $attempt->refresh();
        $this->assertEquals('completed', $attempt->status);
    }

    /** @test */
    public function student_cannot_start_quiz_if_max_attempts_reached()
    {
        $this->actingAs($this->student);
        
        // Create max attempts
        for ($i = 1; $i <= $this->quiz->max_attempts; $i++) {
            QuizAttempt::factory()->create([
                'quiz_id' => $this->quiz->id,
                'user_id' => $this->student->id,
                'status' => 'completed'
            ]);
        }
        
        $response = $this->get(route('student.quizzes.start', $this->quiz->id));
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function student_can_view_quiz_result()
    {
        $this->actingAs($this->student);
        
        $attempt = QuizAttempt::factory()->create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->student->id,
            'status' => 'completed',
            'score' => 80,
            'total_points' => 100,
            'percentage' => 80,
            'passed' => true
        ]);
        
        $response = $this->get(route('student.quizzes.result', $attempt->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('frontend.quiz.result');
    }

    /** @test */
    public function student_can_view_quiz_history()
    {
        $this->actingAs($this->student);
        
        $response = $this->get(route('student.quizzes.history'));
        
        $response->assertStatus(200);
        $response->assertViewIs('frontend.quiz.history');
    }

    /** @test */
    public function student_can_view_quiz_statistics()
    {
        $this->actingAs($this->student);
        
        $response = $this->get(route('student.quizzes.statistics'));
        
        $response->assertStatus(200);
        $response->assertViewIs('frontend.quiz.statistics');
    }

    /** @test */
    public function unauthorized_user_cannot_access_instructor_quiz_management()
    {
        $response = $this->get(route('instructor.quizzes.index'));
        
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function unauthorized_user_cannot_access_student_quiz_pages()
    {
        $response = $this->get(route('student.quizzes.index'));
        
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function student_cannot_access_instructor_quiz_management()
    {
        $this->actingAs($this->student);
        
        $response = $this->get(route('instructor.quizzes.index'));
        
        $response->assertStatus(403);
    }

    /** @test */
    public function instructor_cannot_access_student_quiz_pages()
    {
        $this->actingAs($this->instructor);
        
        $response = $this->get(route('student.quizzes.index'));
        
        $response->assertStatus(403);
    }

    /** @test */
    public function quiz_validation_works_correctly()
    {
        $this->actingAs($this->instructor);
        
        $invalidData = [
            'course_id' => 999, // Non-existent course
            'title' => '', // Empty title
            'type' => 'invalid_type', // Invalid type
            'passing_score' => 150, // Invalid score
            'max_attempts' => 15, // Too many attempts
        ];
        
        $response = $this->post(route('instructor.quizzes.store'), $invalidData);
        
        $response->assertSessionHasErrors([
            'course_id',
            'title',
            'type',
            'passing_score',
            'max_attempts'
        ]);
    }

    /** @test */
    public function question_validation_works_correctly()
    {
        $this->actingAs($this->instructor);
        
        $invalidQuestionData = [
            'question' => '', // Empty question
            'type' => 'multiple_choice',
            'options' => [], // Empty options for multiple choice
            'correct_answers' => ['invalid_key'], // Invalid correct answer
            'points' => 0, // Invalid points
        ];
        
        $response = $this->post(route('instructor.quizzes.questions.store', $this->quiz->id), $invalidQuestionData);
        
        $response->assertSessionHasErrors([
            'question',
            'options',
            'correct_answers',
            'points'
        ]);
    }
} 