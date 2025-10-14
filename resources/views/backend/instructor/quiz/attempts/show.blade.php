@extends('backend.instructor.master')

@section('title', 'Quiz Attempt Details')

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <!-- Breadcrumb -->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Quizzes</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('instructor.quizzes.index') }}">Quizzes</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('instructor.quizzes.show', $quiz->id) }}">{{ $quiz->title }}</a></li>
                        <li class="breadcrumb-item active">Attempt Details</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Attempt Summary -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Attempt Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <strong>Student:</strong> 
                                            <div class="d-flex align-items-center mt-1">
                                                <img src="{{ $attempt->user->profile_photo_url }}" 
                                                     class="rounded-circle me-2" width="40" height="40">
                                                <div>
                                                    <div>{{ $attempt->user->name }}</div>
                                                    <small class="text-muted">{{ $attempt->user->email }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <strong>Quiz:</strong> {{ $quiz->title }}
                                        </div>
                                        <div class="info-item">
                                            <strong>Course:</strong> {{ $quiz->course->title }}
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <strong>Attempt #:</strong> {{ $attempt->attempt_number }}
                                        </div>
                                        <div class="info-item">
                                            <strong>Status:</strong> 
                                            <span class="badge bg-{{ $attempt->status === 'completed' ? 'success' : ($attempt->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                            </span>
                                        </div>
                                        <div class="info-item">
                                            <strong>Started:</strong> {{ $attempt->started_at->format('M d, Y H:i:s') }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        @if($attempt->isCompleted())
                                            <div class="info-item">
                                                <strong>Score:</strong> 
                                                <span class="badge bg-{{ $attempt->passed ? 'success' : 'danger' }} fs-6">
                                                    {{ $attempt->score }}/{{ $attempt->total_points }} ({{ $attempt->percentage }}%)
                                                </span>
                                            </div>
                                            <div class="info-item">
                                                <strong>Result:</strong> 
                                                <span class="badge bg-{{ $attempt->passed ? 'success' : 'danger' }}">
                                                    {{ $attempt->passed ? 'PASSED' : 'FAILED' }}
                                                </span>
                                            </div>
                                            <div class="info-item">
                                                <strong>Completed:</strong> {{ $attempt->completed_at->format('M d, Y H:i:s') }}
                                            </div>
                                        @else
                                            <div class="info-item">
                                                <strong>Time Taken:</strong> 
                                                @if($attempt->time_taken)
                                                    {{ gmdate('H:i:s', $attempt->time_taken) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($attempt->isCompleted())
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar bg-{{ $attempt->passed ? 'success' : 'danger' }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $attempt->percentage }}%"
                                                 aria-valuenow="{{ $attempt->percentage }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ $attempt->percentage }}%
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-1">
                                            <small>0%</small>
                                            <small>{{ $quiz->passing_score }}% (Passing Score)</small>
                                            <small>100%</small>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h6 class="mb-0">Attempt Statistics</h6>
                                    </div>
                                    <div class="card-body">
                                        @if($attempt->isCompleted())
                                            <div class="stat-item text-center">
                                                <h4>{{ $attempt->score }}/{{ $attempt->total_points }}</h4>
                                                <small>Points Earned</small>
                                            </div>
                                            <div class="stat-item text-center mt-3">
                                                <h4>{{ $attempt->percentage }}%</h4>
                                                <small>Percentage</small>
                                            </div>
                                            <div class="stat-item text-center mt-3">
                                                <h4 class="text-{{ $attempt->passed ? 'success' : 'danger' }}">
                                                    {{ $attempt->passed ? 'PASSED' : 'FAILED' }}
                                                </h4>
                                                <small>Result</small>
                                            </div>
                                        @else
                                            <div class="stat-item text-center">
                                                <h4 class="text-warning">IN PROGRESS</h4>
                                                <small>Attempt Status</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question Answers -->
        @if($attempt->isCompleted())
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Question Answers</h5>
                    </div>
                    <div class="card-body">
                        @if($attempt->answers->count() > 0)
                            @foreach($attempt->answers->sortBy('question.order') as $answer)
                                @php
                                    $question = $answer->question;
                                    $isCorrect = $answer->is_correct;
                                @endphp
                                <div class="question-item mb-4 p-3 border rounded {{ $isCorrect ? 'border-success bg-light' : 'border-danger bg-light' }}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0">
                                            Question {{ $question->order }} 
                                            <span class="badge bg-{{ $isCorrect ? 'success' : 'danger' }} ms-2">
                                                {{ $isCorrect ? 'Correct' : 'Incorrect' }}
                                            </span>
                                        </h6>
                                        <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $question->type)) }}</span>
                                    </div>
                                    
                                    <div class="question-text mb-3">
                                        <strong>{{ $question->question }}</strong>
                                    </div>

                                    @if($question->type === 'single_choice' || $question->type === 'multiple_choice')
                                        <div class="options mb-3">
                                            @foreach($question->options as $index => $option)
                                                @php
                                                    $isSelected = in_array($index, $answer->user_answer);
                                                    $isCorrectOption = in_array($index, $question->correct_answers);
                                                @endphp
                                                <div class="option-item d-flex align-items-center p-2 rounded {{ $isSelected ? 'bg-primary text-white' : 'bg-light' }}">
                                                    <div class="me-2">
                                                        @if($isSelected)
                                                            <i class="bx bx-check-circle text-white"></i>
                                                        @else
                                                            <i class="bx bx-circle text-muted"></i>
                                                        @endif
                                                    </div>
                                                    <span>{{ $option }}</span>
                                                    @if($isCorrectOption)
                                                        <i class="bx bx-check text-success ms-auto"></i>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($question->type === 'true_false')
                                        <div class="options mb-3">
                                            @php
                                                $userAnswer = $answer->user_answer[0] ?? null;
                                                $correctAnswer = $question->correct_answers[0] ?? null;
                                            @endphp
                                            <div class="option-item d-flex align-items-center p-2 rounded {{ $userAnswer === 'true' ? 'bg-primary text-white' : 'bg-light' }}">
                                                <div class="me-2">
                                                    @if($userAnswer === 'true')
                                                        <i class="bx bx-check-circle text-white"></i>
                                                    @else
                                                        <i class="bx bx-circle text-muted"></i>
                                                    @endif
                                                </div>
                                                <span>True</span>
                                                @if($correctAnswer === 'true')
                                                    <i class="bx bx-check text-success ms-auto"></i>
                                                @endif
                                            </div>
                                            <div class="option-item d-flex align-items-center p-2 rounded {{ $userAnswer === 'false' ? 'bg-primary text-white' : 'bg-light' }}">
                                                <div class="me-2">
                                                    @if($userAnswer === 'false')
                                                        <i class="bx bx-check-circle text-white"></i>
                                                    @else
                                                        <i class="bx bx-circle text-muted"></i>
                                                    @endif
                                                </div>
                                                <span>False</span>
                                                @if($correctAnswer === 'false')
                                                    <i class="bx bx-check text-success ms-auto"></i>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif($question->type === 'fill_blank')
                                        <div class="answer mb-3">
                                            <strong>Student Answer:</strong>
                                            <div class="p-2 bg-light rounded mt-1">
                                                {{ implode(', ', $answer->user_answer) }}
                                            </div>
                                        </div>
                                        <div class="correct-answer mb-3">
                                            <strong>Correct Answer(s):</strong>
                                            <div class="p-2 bg-success text-white rounded mt-1">
                                                {{ implode(', ', $question->correct_answers) }}
                                            </div>
                                        </div>
                                    @elseif($question->type === 'essay')
                                        <div class="answer mb-3">
                                            <strong>Student Answer:</strong>
                                            <div class="p-2 bg-light rounded mt-1">
                                                {{ implode(', ', $answer->user_answer) }}
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <strong>Points:</strong> {{ $answer->points_earned }}/{{ $question->points }}
                                        </div>
                                    @endif

                                    @if($answer->feedback)
                                        <div class="feedback mb-2">
                                            <strong>Feedback:</strong>
                                            <div class="p-2 bg-info text-white rounded mt-1">
                                                {{ $answer->feedback }}
                                            </div>
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            Answered at: {{ $answer->answered_at->format('M d, Y H:i:s') }}
                                        </small>
                                        <span class="badge bg-{{ $isCorrect ? 'success' : 'danger' }}">
                                            {{ $answer->points_earned }}/{{ $question->points }} points
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="bx bx-question-mark display-1 text-muted"></i>
                                <h5 class="mt-3">No Answers Found</h5>
                                <p class="text-muted">This attempt doesn't have any answers recorded.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('instructor.quizzes.show', $quiz->id) }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back"></i> Back to Quiz
                            </a>
                            <div>
                                @if($attempt->isCompleted())
                                    <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                                        <i class="bx bx-printer"></i> Print Report
                                    </button>
                                @endif
                                <a href="{{ route('instructor.quizzes.attempts.index', $quiz->id) }}" class="btn btn-primary">
                                    <i class="bx bx-list-ul"></i> All Attempts
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.info-item {
    margin-bottom: 1rem;
}

.stat-item h4 {
    color: #6c757d;
    margin-bottom: 0;
}

.stat-item small {
    color: #6c757d;
    font-size: 0.875rem;
}

.question-item {
    transition: all 0.3s ease;
}

.question-item:hover {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.option-item {
    transition: all 0.2s ease;
}

.option-item:hover {
    transform: translateX(5px);
}

@media print {
    .btn, .breadcrumb, .card-header {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endpush 