@extends('frontend.master')

@section('title', 'Available Quizzes')

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header-content">
                    <h1>Available Quizzes</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Quizzes</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quiz List Section -->
<section class="quiz-list-section py-5">
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
            <div class="col-lg-12">
                <div class="quiz-filters mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <select class="form-select" id="courseFilter">
                                <option value="">All Courses</option>
                                @foreach($quizzes->pluck('course.title')->unique() as $courseTitle)
                                    <option value="{{ $courseTitle }}">{{ $courseTitle }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="typeFilter">
                                <option value="">All Types</option>
                                <option value="quiz">Quiz</option>
                                <option value="exam">Exam</option>
                                <option value="assignment">Assignment</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="not_started">Not Started</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100" id="applyFilters">Apply Filters</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="quizContainer">
            @forelse($quizzes as $quiz)
            <div class="col-lg-6 col-xl-4 mb-4 quiz-item" 
                 data-course="{{ $quiz->course->title }}"
                 data-type="{{ $quiz->type }}"
                 data-status="{{ $this->getQuizStatus($quiz) }}">
                <div class="card quiz-card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-{{ $quiz->type === 'quiz' ? 'primary' : ($quiz->type === 'exam' ? 'warning' : 'info') }}">
                                {{ ucfirst($quiz->type) }}
                            </span>
                            @if($quiz->time_limit)
                                <small class="text-muted">
                                    <i class="bx bx-time"></i> {{ $quiz->time_limit }} min
                                </small>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $quiz->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($quiz->description, 100) }}</p>
                        
                        <div class="quiz-meta mb-3">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="meta-item">
                                        <strong>{{ $quiz->question_count }}</strong>
                                        <small class="d-block text-muted">Questions</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="meta-item">
                                        <strong>{{ $quiz->total_points }}</strong>
                                        <small class="d-block text-muted">Points</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="meta-item">
                                        <strong>{{ $quiz->passing_score }}%</strong>
                                        <small class="d-block text-muted">Pass</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="quiz-progress mb-3">
                            @php
                                $attempts = $quiz->attempts;
                                $bestAttempt = $attempts->where('status', 'completed')->sortByDesc('percentage')->first();
                                $attemptCount = $attempts->count();
                            @endphp
                            
                            @if($attemptCount > 0)
                                <div class="progress mb-2">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $bestAttempt ? $bestAttempt->percentage : 0 }}%">
                                        {{ $bestAttempt ? round($bestAttempt->percentage, 1) : 0 }}%
                                    </div>
                                </div>
                                <small class="text-muted">
                                    Best Score: {{ $bestAttempt ? round($bestAttempt->percentage, 1) : 0 }}% 
                                    ({{ $attemptCount }} attempt{{ $attemptCount > 1 ? 's' : '' }})
                                </small>
                            @else
                                <div class="text-center">
                                    <span class="badge bg-secondary">Not Attempted</span>
                                </div>
                            @endif
                        </div>

                        <div class="course-info mb-3">
                            <small class="text-muted">
                                <i class="bx bx-book-open"></i> {{ $quiz->course->title }}
                            </small>
                        </div>
                    </div>
                    <div class="card-footer">
                        @if($quiz->isAvailableForUser(auth()->id()))
                            @if($attemptCount < $quiz->max_attempts)
                                <a href="{{ route('student.quizzes.start', $quiz->id) }}" 
                                   class="btn btn-primary w-100">
                                    <i class="bx bx-play"></i> Start Quiz
                                </a>
                            @else
                                <button class="btn btn-secondary w-100" disabled>
                                    <i class="bx bx-lock"></i> Max Attempts Reached
                                </button>
                            @endif
                        @else
                            <button class="btn btn-secondary w-100" disabled>
                                <i class="bx bx-lock"></i> Not Available
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bx bx-question-mark display-1 text-muted"></i>
                    <h4 class="mt-3">No Quizzes Available</h4>
                    <p class="text-muted">You don't have any quizzes assigned to your enrolled courses yet.</p>
                </div>
            </div>
            @endforelse
        </div>

        @if($quizzes->hasPages())
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $quizzes->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Filter functionality
    $('#applyFilters').click(function() {
        const courseFilter = $('#courseFilter').val();
        const typeFilter = $('#typeFilter').val();
        const statusFilter = $('#statusFilter').val();

        $('.quiz-item').each(function() {
            const $item = $(this);
            const course = $item.data('course');
            const type = $item.data('type');
            const status = $item.data('status');

            let show = true;

            if (courseFilter && course !== courseFilter) show = false;
            if (typeFilter && type !== typeFilter) show = false;
            if (statusFilter && status !== statusFilter) show = false;

            $item.toggle(show);
        });
    });

    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush

@push('styles')
<style>
.quiz-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #e9ecef;
}

.quiz-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.quiz-meta .meta-item {
    padding: 10px 0;
}

.quiz-meta .meta-item strong {
    font-size: 1.2rem;
    color: #495057;
}

.quiz-progress .progress {
    height: 8px;
    border-radius: 4px;
}

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
</style>
@endpush 