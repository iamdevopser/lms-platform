@extends('backend.instructor.master')

@section('title', 'Edit Quiz')

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
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Edit Quiz Form -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Edit Quiz: {{ $quiz->title }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('instructor.quizzes.update', $quiz->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Quiz Title *</label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                               id="title" name="title" value="{{ old('title', $quiz->title) }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" name="description" rows="4">{{ old('description', $quiz->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="course_id" class="form-label">Course *</label>
                                                <select class="form-select @error('course_id') is-invalid @enderror" 
                                                        id="course_id" name="course_id" required>
                                                    <option value="">Select Course</option>
                                                    @foreach($courses as $course)
                                                        <option value="{{ $course->id }}" 
                                                                {{ old('course_id', $quiz->course_id) == $course->id ? 'selected' : '' }}>
                                                            {{ $course->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('course_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="type" class="form-label">Quiz Type *</label>
                                                <select class="form-select @error('type') is-invalid @enderror" 
                                                        id="type" name="type" required>
                                                    <option value="quiz" {{ old('type', $quiz->type) === 'quiz' ? 'selected' : '' }}>Quiz</option>
                                                    <option value="exam" {{ old('type', $quiz->type) === 'exam' ? 'selected' : '' }}>Exam</option>
                                                    <option value="assignment" {{ old('type', $quiz->type) === 'assignment' ? 'selected' : '' }}>Assignment</option>
                                                </select>
                                                @error('type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                                                <input type="number" class="form-control @error('time_limit') is-invalid @enderror" 
                                                       id="time_limit" name="time_limit" 
                                                       value="{{ old('time_limit', $quiz->time_limit) }}" 
                                                       min="1" placeholder="No limit">
                                                <small class="form-text text-muted">Leave empty for no time limit</small>
                                                @error('time_limit')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="passing_score" class="form-label">Passing Score (%) *</label>
                                                <input type="number" class="form-control @error('passing_score') is-invalid @enderror" 
                                                       id="passing_score" name="passing_score" 
                                                       value="{{ old('passing_score', $quiz->passing_score) }}" 
                                                       min="0" max="100" required>
                                                @error('passing_score')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="max_attempts" class="form-label">Maximum Attempts *</label>
                                                <input type="number" class="form-control @error('max_attempts') is-invalid @enderror" 
                                                       id="max_attempts" name="max_attempts" 
                                                       value="{{ old('max_attempts', $quiz->max_attempts) }}" 
                                                       min="1" required>
                                                @error('max_attempts')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="start_date" class="form-label">Start Date</label>
                                                <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                                       id="start_date" name="start_date" 
                                                       value="{{ old('start_date', $quiz->start_date ? $quiz->start_date->format('Y-m-d\TH:i') : '') }}">
                                                <small class="form-text text-muted">Leave empty for immediate availability</small>
                                                @error('start_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="end_date" class="form-label">End Date</label>
                                        <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                               id="end_date" name="end_date" 
                                               value="{{ old('end_date', $quiz->end_date ? $quiz->end_date->format('Y-m-d\TH:i') : '') }}">
                                        <small class="form-text text-muted">Leave empty for no end date</small>
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <h6 class="mb-0">Quiz Settings</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="shuffle_questions" 
                                                       name="shuffle_questions" {{ old('shuffle_questions', $quiz->shuffle_questions) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="shuffle_questions">
                                                    Shuffle Questions
                                                </label>
                                                <small class="form-text text-muted d-block">
                                                    Randomize question order for each attempt
                                                </small>
                                            </div>

                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="show_correct_answers" 
                                                       name="show_correct_answers" {{ old('show_correct_answers', $quiz->show_correct_answers) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="show_correct_answers">
                                                    Show Correct Answers
                                                </label>
                                                <small class="form-text text-muted d-block">
                                                    Display correct answers after completion
                                                </small>
                                            </div>

                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="show_results_immediately" 
                                                       name="show_results_immediately" {{ old('show_results_immediately', $quiz->show_results_immediately) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="show_results_immediately">
                                                    Show Results Immediately
                                                </label>
                                                <small class="form-text text-muted d-block">
                                                    Display results right after submission
                                                </small>
                                            </div>

                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="is_active" 
                                                       name="is_active" {{ old('is_active', $quiz->is_active) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    Active
                                                </label>
                                                <small class="form-text text-muted d-block">
                                                    Make quiz available to students
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card bg-light mt-3">
                                        <div class="card-header">
                                            <h6 class="mb-0">Quiz Statistics</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="stat-item text-center">
                                                <h4>{{ $quiz->questions->count() }}</h4>
                                                <small>Questions</small>
                                            </div>
                                            <div class="stat-item text-center mt-2">
                                                <h4>{{ $quiz->attempts->count() }}</h4>
                                                <small>Total Attempts</small>
                                            </div>
                                            <div class="stat-item text-center mt-2">
                                                <h4>{{ number_format($quiz->getAverageScoreAttribute(), 1) }}%</h4>
                                                <small>Average Score</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('instructor.quizzes.show', $quiz->id) }}" class="btn btn-secondary">
                                            <i class="bx bx-arrow-back"></i> Back to Quiz
                                        </a>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bx bx-save"></i> Update Quiz
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.stat-item h4 {
    color: #6c757d;
    margin-bottom: 0;
}

.stat-item small {
    color: #6c757d;
    font-size: 0.875rem;
}
</style>
@endpush

@push('scripts')
<script>
// Date validation
document.getElementById('start_date').addEventListener('change', function() {
    const startDate = this.value;
    const endDate = document.getElementById('end_date').value;
    
    if (startDate && endDate && startDate >= endDate) {
        alert('Start date must be before end date');
        this.value = '';
    }
});

document.getElementById('end_date').addEventListener('change', function() {
    const endDate = this.value;
    const startDate = document.getElementById('start_date').value;
    
    if (startDate && endDate && startDate >= endDate) {
        alert('End date must be after start date');
        this.value = '';
    }
});
</script>
@endpush 