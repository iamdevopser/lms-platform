<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Course;
use App\Models\User;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some courses and instructors
        $courses = Course::take(5)->get();
        $instructors = User::where('role', 'instructor')->take(3)->get();

        if ($courses->isEmpty() || $instructors->isEmpty()) {
            $this->command->info('No courses or instructors found. Skipping quiz seeding.');
            return;
        }

        $quizTypes = ['quiz', 'exam', 'assignment'];
        $questionTypes = ['multiple_choice', 'single_choice', 'true_false', 'fill_blank'];

        foreach ($courses as $course) {
            // Create 2-3 quizzes per course
            $quizCount = rand(2, 3);
            
            for ($i = 1; $i <= $quizCount; $i++) {
                $quiz = Quiz::create([
                    'course_id' => $course->id,
                    'title' => $this->getQuizTitle($i, $course->title),
                    'description' => $this->getQuizDescription($i),
                    'type' => $quizTypes[array_rand($quizTypes)],
                    'time_limit' => rand(0, 1) ? rand(15, 120) : null, // 15-120 minutes or no limit
                    'passing_score' => rand(60, 85),
                    'max_attempts' => rand(1, 5),
                    'shuffle_questions' => rand(0, 1),
                    'show_correct_answers' => rand(0, 1),
                    'show_results_immediately' => rand(0, 1),
                    'is_active' => true,
                    'start_date' => now()->subDays(rand(0, 30)),
                    'end_date' => now()->addDays(rand(30, 90)),
                ]);

                // Create 5-10 questions per quiz
                $questionCount = rand(5, 10);
                $this->createQuestions($quiz, $questionCount, $questionTypes);
            }
        }

        $this->command->info('Quiz seeder completed successfully!');
    }

    /**
     * Create questions for a quiz
     */
    private function createQuestions($quiz, $count, $types)
    {
        for ($i = 1; $i <= $count; $i++) {
            $type = $types[array_rand($types)];
            $questionData = $this->getQuestionData($type, $i);
            
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => $questionData['question'],
                'type' => $type,
                'options' => $questionData['options'] ?? null,
                'correct_answers' => $questionData['correct_answers'] ?? null,
                'explanation' => $questionData['explanation'] ?? null,
                'points' => rand(1, 5),
                'order' => $i,
                'is_active' => true,
            ]);
        }
    }

    /**
     * Get quiz title
     */
    private function getQuizTitle($number, $courseTitle)
    {
        $titles = [
            "Chapter {$number} Assessment",
            "Midterm {$number}",
            "Final {$number}",
            "Practice Quiz {$number}",
            "Knowledge Check {$number}",
            "Review Test {$number}",
        ];
        
        return $titles[array_rand($titles)] . " - " . $courseTitle;
    }

    /**
     * Get quiz description
     */
    private function getQuizDescription($number)
    {
        $descriptions = [
            "Test your knowledge of the course material covered in this section.",
            "Comprehensive assessment to evaluate your understanding of key concepts.",
            "Practice questions to help reinforce your learning.",
            "Review and assessment of important topics covered in the course.",
            "Evaluation of your progress and understanding of the subject matter.",
        ];
        
        return $descriptions[array_rand($descriptions)];
    }

    /**
     * Get question data based on type
     */
    private function getQuestionData($type, $number)
    {
        switch ($type) {
            case 'multiple_choice':
                return $this->getMultipleChoiceQuestion($number);
            case 'single_choice':
                return $this->getSingleChoiceQuestion($number);
            case 'true_false':
                return $this->getTrueFalseQuestion($number);
            case 'fill_blank':
                return $this->getFillBlankQuestion($number);
            default:
                return $this->getMultipleChoiceQuestion($number);
        }
    }

    /**
     * Get multiple choice question
     */
    private function getMultipleChoiceQuestion($number)
    {
        $questions = [
            "What is the primary purpose of learning management systems?",
            "Which of the following is NOT a key feature of modern LMS platforms?",
            "What are the main benefits of online learning?",
            "Which technology is commonly used for video conferencing in e-learning?",
            "What is the role of gamification in education?",
        ];

        $options = [
            "A" => "To replace traditional classrooms completely",
            "B" => "To enhance and support traditional learning methods",
            "C" => "To eliminate the need for teachers",
            "D" => "To reduce educational costs only",
        ];

        $correctAnswers = ["B"];

        return [
            'question' => $questions[($number - 1) % count($questions)],
            'options' => $options,
            'correct_answers' => $correctAnswers,
            'explanation' => "Learning management systems are designed to enhance and support traditional learning methods, not replace them entirely.",
        ];
    }

    /**
     * Get single choice question
     */
    private function getSingleChoiceQuestion($number)
    {
        $questions = [
            "What is the most important factor for successful online learning?",
            "Which learning style is most effective for online education?",
            "What is the primary advantage of asynchronous learning?",
            "Which assessment method is best for online courses?",
            "What is the key to maintaining student engagement online?",
        ];

        $options = [
            "A" => "High-speed internet",
            "B" => "Self-discipline and motivation",
            "C" => "Expensive equipment",
            "D" => "Social media presence",
        ];

        $correctAnswers = ["B"];

        return [
            'question' => $questions[($number - 1) % count($questions)],
            'options' => $options,
            'correct_answers' => $correctAnswers,
            'explanation' => "Self-discipline and motivation are crucial for successful online learning as students need to manage their own time and stay focused.",
        ];
    }

    /**
     * Get true/false question
     */
    private function getTrueFalseQuestion($number)
    {
        $questions = [
            "Online learning is always more effective than traditional classroom learning.",
            "Students can learn at their own pace in online courses.",
            "Online courses require less time commitment than traditional courses.",
            "Interactive elements improve online learning outcomes.",
            "All online courses are self-paced.",
        ];

        $correctAnswers = ["false", "true", "false", "true", "false"];

        return [
            'question' => $questions[($number - 1) % count($questions)],
            'correct_answers' => [$correctAnswers[($number - 1) % count($correctAnswers)]],
            'explanation' => "This statement requires careful consideration of the specific context and course design.",
        ];
    }

    /**
     * Get fill in the blank question
     */
    private function getFillBlankQuestion($number)
    {
        $questions = [
            "The acronym LMS stands for __________ Management System.",
            "__________ learning allows students to access content at any time.",
            "The most important skill for online learning is __________.",
            "__________ feedback helps students improve their performance.",
            "Online courses often use __________ to track student progress.",
        ];

        $correctAnswers = ["Learning", "Asynchronous", "self-discipline", "Constructive", "analytics"];

        return [
            'question' => $questions[($number - 1) % count($questions)],
            'correct_answers' => [$correctAnswers[($number - 1) % count($correctAnswers)]],
            'explanation' => "This question tests your understanding of key e-learning terminology and concepts.",
        ];
    }
} 