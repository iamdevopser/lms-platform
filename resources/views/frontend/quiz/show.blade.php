@extends('frontend.master')

@section('title', $quiz->title)

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header-content">
                    <h1>{{ $quiz->title }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('student.quizzes.index') }}">Quizzes</a></li>
                            <li class="breadcrumb-item active">{{ $quiz->title }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quiz Details Section -->
<section class="quiz-details-section py-5">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Quiz Information -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="mb-0">Quiz Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>{{ $quiz->title }}</h5>
                                <p class="text-muted">{{ $quiz->description }}</p>
                                
                                <div class="quiz-meta">
                                    <div class="meta-item mb-2">
                                        <i class="bx bx-book-open text-primary"></i>
                                        <strong>Course:</strong> {{ $quiz->course->title }}
                                    </div>
                                    <div class="meta-item mb-2">
                                        <i class="bx bx-target-lock text-warning"></i>
                                        <strong>Type:</strong> 
                                        <span class="badge bg-{{ $quiz->type === 'quiz' ? 'primary' : ($quiz->type === 'exam' ? 'warning' : 'info') }}">
                                            {{ ucfirst($quiz->type) }}
                                        </span>
                                    </div>
                                    <div class="meta-item mb-2">
                                        <i class="bx bx-time text-info"></i>
                                        <strong>Time Limit:</strong> 
                                        {{ $quiz->time_limit ? $quiz->time_limit . ' minutes' : 'No time limit' }}
                                    </div>
                                    <div class="meta-item mb-2">
                                        <i class="bx bx-check-circle text-success"></i>
                                        <strong>Passing Score:</strong> {{ $quiz->passing_score }}%
                                    </div>
                                    <div class="meta-item mb-2">
                                        <i class="bx bx-refresh text-secondary"></i>
                                        <strong>Max Attempts:</strong> {{ $quiz->max_attempts }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="quiz-stats">
                                    <div class="stat-card text-center p-3 bg-light rounded">
                                        <h3 class="text-primary mb-1">{{ $quiz->question_count }}</h3>
                                        <small class="text-muted">Total Questions</small>
                                    </div>
                                    <div class="stat-card text-center p-3 bg-light rounded mt-3">
                                        <h3 class="text-success mb-1">{{ $quiz->total_points }}</h3>
                                        <small class="text-muted">Total Points</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quiz Instructions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Instructions</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="bx bx-check text-success"></i>
                                Read each question carefully before answering
                            </li>
                            <li class="mb-2">
                                <i class="bx bx-check text-success"></i>
                                You have {{ $quiz->max_attempts }} attempt{{ $quiz->max_attempts > 1 ? 's' : '' }} to complete this quiz
                            </li>
                            @if($quiz->time_limit)
                            <li class="mb-2">
                                <i class="bx bx-check text-success"></i>
                                Time limit: {{ $quiz->time_limit }} minutes - timer will start when you begin
                            </li>
                            @endif
                            <li class="mb-2">
                                <i class="bx bx-check text-success"></i>
                                Passing score required: {{ $quiz->passing_score }}%
                            </li>
                            @if($quiz->shuffle_questions)
                            <li class="mb-2">
                                <i class="bx bx-check text-success"></i>
                                Questions will be presented in random order
                            </li>
                            @endif
                            @if($quiz->show_results_immediately)
                            <li class="mb-2">
                                <i class="bx bx-check text-success"></i>
                                Results will be shown immediately after completion
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Course Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Course Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <img src="{{ $quiz->course->image ? asset('upload/course/' . $quiz->course->image) : asset('frontend/images/default-course.jpg') }}" 
                                     alt="{{ $quiz->course->title }}" class="img-fluid rounded">
                            </div>
                            <div class="col-md-8">
                                <h6>{{ $quiz->course->title }}</h6>
                                <p class="text-muted">{{ Str::limit($quiz->course->description, 150) }}</p>
                                <div class="course-meta">
                                    <span class="badge bg-primary me-2">{{ $quiz->course->level }}</span>
                                    <span class="badge bg-info me-2">{{ $quiz->course->duration }}</span>
                                    <span class="badge bg-success">{{ $quiz->course->lessons_count }} Lessons</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiz Actions & History -->
            <div class="col-lg-4">
                <div class="position-sticky" style="top: 2rem;">
                    <!-- Quiz Actions -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Quiz Actions</h6>
                        </div>
                        <div class="card-body">
                            @if($quiz->isAvailableForUser(auth()->id()))
                                @php
                                    $attemptCount = $attempts->count();
                                    $inProgressAttempt = $attempts->where('status', 'in_progress')->first();
                                @endphp

                                @if($inProgressAttempt)
                                    <div class="text-center mb-3">
                                        <div class="alert alert-warning">
                                            <i class="bx bx-time"></i>
                                            <strong>Quiz in Progress</strong><br>
                                            <small>You have an unfinished attempt</small>
                                        </div>
                                        <a href="{{ route('student.quizzes.resume', $inProgressAttempt->id) }}" 
                                           class="btn btn-warning w-100 mb-2">
                                            <i class="bx bx-play"></i> Resume Quiz
                                        </a>
                                        <form action="{{ route('student.quizzes.abandon', $inProgressAttempt->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger w-100" 
                                                    onclick="return confirm('Are you sure you want to abandon this attempt?')">
                                                <i class="bx bx-x"></i> Abandon Attempt
                                            </button>
                                        </form>
                                    </div>
                                @elseif($attemptCount < $quiz->max_attempts)
                                    <div class="text-center">
                                        <a href="{{ route('student.quizzes.start', $quiz->id) }}" 
                                           class="btn btn-primary btn-lg w-100">
                                            <i class="bx bx-play"></i> Start Quiz
                                        </a>
                                        <small class="text-muted d-block mt-2">
                                            Attempt {{ $attemptCount + 1 }} of {{ $quiz->max_attempts }}
                                        </small>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <div class="alert alert-secondary">
                                            <i class="bx bx-lock"></i>
                                            <strong>Max Attempts Reached</strong><br>
                                            <small>You have used all {{ $quiz->max_attempts }} attempts</small>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="text-center">
                                    <div class="alert alert-danger">
                                        <i class="bx bx-lock"></i>
                                        <strong>Quiz Not Available</strong><br>
                                        <small>This quiz is not currently available for you</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quiz History -->
                    @if($attempts->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Attempt History</h6>
                        </div>
                        <div class="card-body">
                            @foreach($attempts->take(5) as $attempt)
                            <div class="attempt-item mb-3 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Attempt {{ $attempt->attempt_number }}</strong>
                                    <span class="badge bg-{{ $attempt->status === 'completed' ? 'success' : ($attempt->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                    </span>
                                </div>
                                
                                @if($attempt->status === 'completed')
                                    <div class="attempt-result">
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="result-item">
                                                    <strong class="text-primary">{{ $attempt->score }}</strong>
                                                    <small class="d-block text-muted">Score</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="result-item">
                                                    <strong class="text-{{ $attempt->passed ? 'success' : 'danger' }}">
                                                        {{ round($attempt->percentage, 1) }}%
                                                    </strong>
                                                    <small class="d-block text-muted">Percentage</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="result-item">
                                                    <strong class="text-info">{{ $attempt->formatted_time_taken }}</strong>
                                                    <small class="d-block text-muted">Time</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($attempt->passed)
                                            <div class="text-center mt-2">
                                                <span class="badge bg-success">
                                                    <i class="bx bx-check"></i> Passed
                                                </span>
                                            </div>
                                        @else
                                            <div class="text-center mt-2">
                                                <span class="badge bg-danger">
                                                    <i class="bx bx-x"></i> Failed
                                                </span>
                                            </div>
                                        @endif
                                        
                                        <div class="text-center mt-2">
                                            <a href="{{ route('student.quizzes.review', $attempt->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bx bx-search"></i> Review
                                            </a>
                                        </div>
                                    </div>
                                @elseif($attempt->status === 'in_progress')
                                    <div class="text-center">
                                        <small class="text-muted">Started {{ $attempt->started_at->diffForHumans() }}</small>
                                    </div>
                                @endif
                            </div>
                            @endforeach
                            
                            @if($attempts->count() > 5)
                                <div class="text-center">
                                    <a href="{{ route('student.quizzes.history') }}" class="btn btn-sm btn-outline-secondary">
                                        View All Attempts
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Best Performance -->
                    @if($bestAttempt)
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Best Performance</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="best-score mb-3">
                                <div class="score-circle mx-auto">
                                    <span class="score-percentage">{{ round($bestAttempt->percentage, 1) }}%</span>
                                </div>
                            </div>
                            <h6 class="text-success mb-2">
                                <i class="bx bx-trophy"></i> Best Score
                            </h6>
                            <p class="mb-1">
                                <strong>{{ $bestAttempt->score }}</strong> out of <strong>{{ $bestAttempt->total_points }}</strong> points
                            </p>
                            <small class="text-muted">
                                Attempt {{ $bestAttempt->attempt_number }} • {{ $bestAttempt->formatted_time_taken }}
                            </small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
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

.quiz-meta .meta-item {
    margin-bottom: 10px;
}

.quiz-meta .meta-item i {
    width: 20px;
    margin-right: 8px;
}

.stat-card {
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.attempt-item {
    transition: all 0.2s;
}

.attempt-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.result-item strong {
    font-size: 1.2rem;
}

.score-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: conic-gradient(#28a745 0deg, #28a745 {{ $bestAttempt ? ($bestAttempt->percentage / 100) * 360 : 0 }}deg, #e9ecef {{ $bestAttempt ? ($bestAttempt->percentage / 100) * 360 : 0 }}deg, #e9ecef 360deg);
    display: flex;
    align-items: center;
    justify-content: center;
}

.score-circle .score-percentage {
    font-size: 1.1rem;
    font-weight: bold;
    color: #28a745;
}

@media (max-width: 768px) {
    .col-lg-4 {
        margin-top: 2rem;
    }
    
    .position-sticky {
        position: static !important;
    }
}
</style>
@endpush 