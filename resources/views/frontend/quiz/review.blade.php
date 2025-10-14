@extends('frontend.master')

@section('title', 'Review Quiz: ' . $attempt->quiz->title)

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header-content">
                    <h1>Quiz Review</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('student.quizzes.index') }}">Quizzes</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('student.quizzes.show', $attempt->quiz->id) }}">{{ $attempt->quiz->title }}</a></li>
                            <li class="breadcrumb-item active">Review</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quiz Review Section -->
<section class="quiz-review-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Review Header -->
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">{{ $attempt->quiz->title }}</h4>
                                <small class="text-muted">{{ $attempt->quiz->course->title }}</small>
                            </div>
                            <div class="text-end">
                                <div class="score-badge">
                                    <span class="badge bg-{{ $attempt->passed ? 'success' : 'danger' }} fs-6">
                                        {{ round($attempt->percentage, 1) }}%
                                    </span>
                                </div>
                                <small class="d-block text-muted">
                                    {{ $attempt->score }}/{{ $attempt->total_points }} points
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-0">{{ $attempt->quiz->description }}</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="quiz-meta">
                                    <span class="badge bg-primary me-2">{{ ucfirst($attempt->quiz->type) }}</span>
                                    <span class="badge bg-info me-2">{{ $attempt->quiz->question_count }} Questions</span>
                                    <span class="badge bg-warning">{{ $attempt->quiz->total_points }} Points</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions Review -->
                @foreach($attempt->quiz->activeQuestions as $index => $question)
                @php
                    $userAnswer = $attempt->getAnswerForQuestion($question->id);
                    $isCorrect = $userAnswer ? $userAnswer->is_correct : null;
                @endphp
                
                <div class="card mb-4 question-review-card {{ $isCorrect === true ? 'border-success' : ($isCorrect === false ? 'border-danger' : 'border-warning') }}">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Question {{ $index + 1 }}</h5>
                            <div class="question-status">
                                @if($isCorrect === true)
                                    <span class="badge bg-success">
                                        <i class="bx bx-check"></i> Correct
                                    </span>
                                @elseif($isCorrect === false)
                                    <span class="badge bg-danger">
                                        <i class="bx bx-x"></i> Incorrect
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="bx bx-time"></i> Pending
                                    </span>
                                @endif
                                <span class="badge bg-primary ms-2">{{ $question->points }} point{{ $question->points > 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Question Content -->
                        <div class="question-content mb-4">
                            <p class="h6">{{ $question->question }}</p>
                        </div>

                        <!-- Question Type Display -->
                        <div class="question-type mb-3">
                            <span class="badge bg-secondary">{{ $question->type_display_name }}</span>
                        </div>

                        <!-- User Answer -->
                        <div class="user-answer mb-3">
                            <h6 class="text-primary">Your Answer:</h6>
                            <div class="answer-display">
                                @if($userAnswer && !empty($userAnswer->user_answer))
                                    <p class="mb-0">{{ $userAnswer->formatted_user_answer }}</p>
                                @else
                                    <p class="text-muted mb-0">No answer provided</p>
                                @endif
                            </div>
                        </div>

                        <!-- Correct Answer -->
                        @if($attempt->quiz->show_correct_answers)
                        <div class="correct-answer mb-3">
                            <h6 class="text-success">Correct Answer:</h6>
                            <div class="answer-display">
                                <p class="mb-0">{{ $userAnswer ? $userAnswer->correct_answer_display : 'Not available' }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Points Earned -->
                        <div class="points-earned mb-3">
                            <h6 class="text-info">Points:</h6>
                            <div class="points-display">
                                @if($userAnswer)
                                    <span class="badge bg-{{ $userAnswer->is_correct ? 'success' : 'danger' }}">
                                        {{ $userAnswer->points_earned }}/{{ $question->points }} points
                                    </span>
                                @else
                                    <span class="badge bg-secondary">0/{{ $question->points }} points</span>
                                @endif
                            </div>
                        </div>

                        <!-- Explanation -->
                        @if($question->explanation)
                        <div class="explanation mb-3">
                            <h6 class="text-warning">Explanation:</h6>
                            <div class="explanation-content">
                                <p class="mb-0">{{ $question->explanation }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Question Options (for multiple choice) -->
                        @if(in_array($question->type, ['multiple_choice', 'single_choice']) && !empty($question->options))
                        <div class="question-options">
                            <h6 class="text-muted">Options:</h6>
                            <div class="options-list">
                                @foreach($question->formatted_options as $option)
                                <div class="option-item {{ in_array($option['key'], $question->correct_answers ?? []) ? 'correct-option' : '' }}">
                                    <span class="option-key me-2">{{ $option['key'] }}.</span>
                                    <span class="option-value">{{ $option['value'] }}</span>
                                    @if($option['is_correct'])
                                        <i class="bx bx-check-circle text-success ms-2"></i>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach

                <!-- Review Actions -->
                <div class="card">
                    <div class="card-body text-center">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <a href="{{ route('student.quizzes.result', $attempt->id) }}" 
                                   class="btn btn-outline-primary w-100">
                                    <i class="bx bx-arrow-back"></i> Back to Result
                                </a>
                            </div>
                            <div class="col-md-4 mb-2">
                                <a href="{{ route('student.quizzes.show', $attempt->quiz->id) }}" 
                                   class="btn btn-outline-secondary w-100">
                                    <i class="bx bx-list-ul"></i> Quiz Details
                                </a>
                            </div>
                            <div class="col-md-4 mb-2">
                                <a href="{{ route('student.quizzes.index') }}" 
                                   class="btn btn-outline-info w-100">
                                    <i class="bx bx-home"></i> All Quizzes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Sidebar -->
            <div class="col-lg-4">
                <div class="position-sticky" style="top: 2rem;">
                    <!-- Score Summary -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Score Summary</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="score-circle-large mb-3">
                                <span class="score-percentage-large">{{ round($attempt->percentage, 1) }}%</span>
                            </div>
                            <h5 class="mb-2">{{ $attempt->score }}/{{ $attempt->total_points }} Points</h5>
                            <div class="score-breakdown">
                                <div class="score-item mb-2">
                                    <span class="text-success">{{ $attempt->answers->where('is_correct', true)->count() }} Correct</span>
                                </div>
                                <div class="score-item mb-2">
                                    <span class="text-danger">{{ $attempt->answers->where('is_correct', false)->count() }} Incorrect</span>
                                </div>
                                @if($attempt->answers->whereNull('is_correct')->count() > 0)
                                <div class="score-item mb-2">
                                    <span class="text-warning">{{ $attempt->answers->whereNull('is_correct')->count() }} Pending</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Performance Chart -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Performance</h6>
                        </div>
                        <div class="card-body">
                            <div class="performance-chart">
                                <canvas id="performanceChart" width="200" height="200"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Quiz Statistics -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Quiz Statistics</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="bx bx-time text-info"></i>
                                    <strong>Time Taken:</strong> {{ $attempt->formatted_time_taken }}
                                </li>
                                <li class="mb-2">
                                    <i class="bx bx-calendar text-primary"></i>
                                    <strong>Completed:</strong> {{ $attempt->completed_at->format('M d, Y H:i') }}
                                </li>
                                <li class="mb-2">
                                    <i class="bx bx-target-lock text-warning"></i>
                                    <strong>Passing Score:</strong> {{ $attempt->quiz->passing_score }}%
                                </li>
                                <li class="mb-2">
                                    <i class="bx bx-check-circle text-{{ $attempt->passed ? 'success' : 'danger' }}"></i>
                                    <strong>Result:</strong> {{ $attempt->passed ? 'Passed' : 'Failed' }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Performance Chart
    const ctx = document.getElementById('performanceChart').getContext('2d');
    const correctCount = {{ $attempt->answers->where('is_correct', true)->count() }};
    const incorrectCount = {{ $attempt->answers->where('is_correct', false)->count() }};
    const pendingCount = {{ $attempt->answers->whereNull('is_correct')->count() }};
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Correct', 'Incorrect', 'Pending'],
            datasets: [{
                data: [correctCount, incorrectCount, pendingCount],
                backgroundColor: ['#28a745', '#dc3545', '#ffc107'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 60px 0;
}

.page-header-content h1 {
    margin: 0;
    font-size: 2.5rem;
    font-weight: 600;
}

.breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255,255,255,0.7);
}

.breadcrumb-item a {
    color: rgba(255,255,255,0.9);
    text-decoration: none;
}

.breadcrumb-item.active {
    color: rgba(255,255,255,0.7);
}

.question-review-card {
    transition: all 0.2s;
}

.question-review-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.question-status {
    display: flex;
    align-items: center;
    gap: 10px;
}

.answer-display {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.points-display {
    margin-top: 5px;
}

.explanation-content {
    background: #fff3cd;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #ffc107;
}

.options-list {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.option-item {
    padding: 8px 12px;
    margin-bottom: 8px;
    border-radius: 6px;
    background: white;
    border: 1px solid #dee2e6;
}

.option-item.correct-option {
    background: #d4edda;
    border-color: #c3e6cb;
}

.option-key {
    font-weight: bold;
    color: #007bff;
}

.score-circle-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: conic-gradient(#28a745 0deg, #28a745 {{ ($attempt->percentage / 100) * 360 }}deg, #e9ecef {{ ($attempt->percentage / 100) * 360 }}deg, #e9ecef 360deg);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.score-percentage-large {
    font-size: 1.8rem;
    font-weight: bold;
    color: #28a745;
}

.score-breakdown .score-item {
    font-size: 0.9rem;
}

.performance-chart {
    height: 200px;
    position: relative;
}

@media (max-width: 768px) {
    .col-lg-4 {
        margin-top: 2rem;
    }
    
    .position-sticky {
        position: static !important;
    }
    
    .score-circle-large {
        width: 100px;
        height: 100px;
    }
    
    .score-percentage-large {
        font-size: 1.5rem;
    }
}
</style>
@endpush 