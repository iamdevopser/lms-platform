@extends('frontend.master')

@section('title', 'Quiz Result')

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header-content">
                    <h1>Quiz Result</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('student.quizzes.index') }}">Quizzes</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('student.quizzes.show', $attempt->quiz->id) }}">{{ $attempt->quiz->title }}</a></li>
                            <li class="breadcrumb-item active">Result</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quiz Result Section -->
<section class="quiz-result-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Result Summary Card -->
                <div class="card result-summary-card mb-4">
                    <div class="card-header text-center">
                        <h3 class="mb-0">Quiz Completed!</h3>
                        <p class="text-muted mb-0">{{ $attempt->quiz->title }}</p>
                    </div>
                    <div class="card-body text-center">
                        <!-- Score Display -->
                        <div class="score-display mb-4">
                            @if($attempt->passed)
                                <div class="result-icon success mb-3">
                                    <i class="bx bx-check-circle"></i>
                                </div>
                                <h2 class="text-success mb-2">Congratulations!</h2>
                                <p class="text-muted">You have successfully passed this quiz</p>
                            @else
                                <div class="result-icon failed mb-3">
                                    <i class="bx bx-x-circle"></i>
                                </div>
                                <h2 class="text-danger mb-2">Keep Learning!</h2>
                                <p class="text-muted">You didn't reach the passing score this time</p>
                            @endif
                        </div>

                        <!-- Score Details -->
                        <div class="score-details mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="score-item">
                                        <div class="score-circle {{ $attempt->passed ? 'success' : 'failed' }}">
                                            <span class="score-percentage">{{ round($attempt->percentage, 1) }}%</span>
                                        </div>
                                        <h5 class="mt-2">Final Score</h5>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="score-item">
                                        <div class="score-circle points">
                                            <span class="score-percentage">{{ $attempt->score }}</span>
                                        </div>
                                        <h5 class="mt-2">Points Earned</h5>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="score-item">
                                        <div class="score-circle time">
                                            <span class="score-percentage">{{ $attempt->formatted_time_taken }}</span>
                                        </div>
                                        <h5 class="mt-2">Time Taken</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Grade Information -->
                        <div class="grade-info mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="grade-item">
                                        <strong>Grade:</strong> 
                                        <span class="badge bg-{{ $attempt->grade_color }} fs-6">{{ $attempt->grade_letter }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="grade-item">
                                        <strong>Passing Score:</strong> {{ $attempt->quiz->passing_score }}%
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Bar -->
                        <div class="performance-bar mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Your Score</span>
                                <span>{{ round($attempt->percentage, 1) }}%</span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-{{ $attempt->passed ? 'success' : 'danger' }}" 
                                     role="progressbar" 
                                     style="width: {{ $attempt->percentage }}%">
                                </div>
                            </div>
                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    @if($attempt->passed)
                                        You scored {{ round($attempt->percentage - $attempt->quiz->passing_score, 1) }}% above the passing mark!
                                    @else
                                        You need {{ round($attempt->quiz->passing_score - $attempt->percentage, 1) }}% more to pass
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quiz Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Quiz Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bx bx-book-open text-primary"></i>
                                        <strong>Course:</strong> {{ $attempt->quiz->course->title }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="bx bx-target-lock text-warning"></i>
                                        <strong>Quiz Type:</strong> {{ ucfirst($attempt->quiz->type) }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="bx bx-time text-info"></i>
                                        <strong>Time Limit:</strong> 
                                        {{ $attempt->quiz->time_limit ? $attempt->quiz->time_limit . ' minutes' : 'No limit' }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bx bx-question-mark text-success"></i>
                                        <strong>Questions:</strong> {{ $attempt->quiz->question_count }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="bx bx-star text-warning"></i>
                                        <strong>Total Points:</strong> {{ $attempt->quiz->total_points }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="bx bx-refresh text-secondary"></i>
                                        <strong>Attempt:</strong> {{ $attempt->attempt_number }} of {{ $attempt->quiz->max_attempts }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <a href="{{ route('student.quizzes.show', $attempt->quiz->id) }}" 
                                   class="btn btn-outline-primary w-100">
                                    <i class="bx bx-arrow-back"></i> Back to Quiz
                                </a>
                            </div>
                            <div class="col-md-4 mb-2">
                                @if($attempt->quiz->show_correct_answers)
                                    <a href="{{ route('student.quizzes.review', $attempt->id) }}" 
                                       class="btn btn-success w-100">
                                        <i class="bx bx-search"></i> Review Answers
                                    </a>
                                @else
                                    <button class="btn btn-secondary w-100" disabled>
                                        <i class="bx bx-lock"></i> Review Not Available
                                    </button>
                                @endif
                            </div>
                            <div class="col-md-4 mb-2">
                                <a href="{{ route('student.quizzes.index') }}" 
                                   class="btn btn-outline-secondary w-100">
                                    <i class="bx bx-list-ul"></i> All Quizzes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attempt Summary -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Attempt Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 mb-3">
                                <div class="summary-item">
                                    <div class="summary-icon text-primary">
                                        <i class="bx bx-calendar"></i>
                                    </div>
                                    <h6 class="mb-1">Started</h6>
                                    <small class="text-muted">{{ $attempt->started_at->format('M d, Y H:i') }}</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="summary-item">
                                    <div class="summary-icon text-success">
                                        <i class="bx bx-check"></i>
                                    </div>
                                    <h6 class="mb-1">Completed</h6>
                                    <small class="text-muted">{{ $attempt->completed_at->format('M d, Y H:i') }}</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="summary-item">
                                    <div class="summary-icon text-info">
                                        <i class="bx bx-time"></i>
                                    </div>
                                    <h6 class="mb-1">Duration</h6>
                                    <small class="text-muted">{{ $attempt->formatted_time_taken }}</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="summary-item">
                                    <div class="summary-icon text-{{ $attempt->passed ? 'success' : 'danger' }}">
                                        <i class="bx bx-trophy"></i>
                                    </div>
                                    <h6 class="mb-1">Status</h6>
                                    <small class="text-muted">{{ $attempt->passed ? 'Passed' : 'Failed' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Add some animation to score circles
    $('.score-circle').each(function(index) {
        $(this).delay(index * 200).animate({
            opacity: 1,
            transform: 'scale(1)'
        }, 500);
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

.result-summary-card {
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.result-icon {
    font-size: 4rem;
}

.result-icon.success {
    color: #28a745;
}

.result-icon.failed {
    color: #dc3545;
}

.score-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    opacity: 0;
    transform: scale(0.8);
}

.score-circle.success {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.score-circle.failed {
    background: linear-gradient(135deg, #dc3545, #fd7e14);
    color: white;
}

.score-circle.points {
    background: linear-gradient(135deg, #007bff, #6610f2);
    color: white;
}

.score-circle.time {
    background: linear-gradient(135deg, #6c757d, #495057);
    color: white;
}

.score-percentage {
    font-size: 1.5rem;
    font-weight: bold;
}

.grade-item {
    padding: 10px 0;
    text-align: center;
}

.performance-bar .progress {
    border-radius: 10px;
}

.summary-item {
    padding: 15px;
}

.summary-icon {
    font-size: 2rem;
    margin-bottom: 10px;
}

.summary-item h6 {
    color: #495057;
    margin-bottom: 5px;
}

@media (max-width: 768px) {
    .score-circle {
        width: 80px;
        height: 80px;
    }
    
    .score-percentage {
        font-size: 1.2rem;
    }
    
    .result-icon {
        font-size: 3rem;
    }
}
</style>
@endpush 