@extends('backend.instructor.master')

@section('title', 'Create Quiz')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Create New Quiz</h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('instructor.quizzes.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Quiz Title *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
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
                                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
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
                                                <option value="">Select Type</option>
                                                <option value="quiz" {{ old('type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                                <option value="exam" {{ old('type') == 'exam' ? 'selected' : '' }}>Exam</option>
                                                <option value="assignment" {{ old('type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Quiz Settings</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                                            <input type="number" class="form-control @error('time_limit') is-invalid @enderror" 
                                                   id="time_limit" name="time_limit" value="{{ old('time_limit') }}" 
                                                   min="1" placeholder="No limit">
                                            @error('time_limit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="passing_score" class="form-label">Passing Score (%) *</label>
                                            <input type="number" class="form-control @error('passing_score') is-invalid @enderror" 
                                                   id="passing_score" name="passing_score" value="{{ old('passing_score', 70) }}" 
                                                   min="0" max="100" required>
                                            @error('passing_score')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="max_attempts" class="form-label">Max Attempts *</label>
                                            <input type="number" class="form-control @error('max_attempts') is-invalid @enderror" 
                                                   id="max_attempts" name="max_attempts" value="{{ old('max_attempts', 3) }}" 
                                                   min="1" max="10" required>
                                            @error('max_attempts')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="shuffle_questions" 
                                                       name="shuffle_questions" {{ old('shuffle_questions') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="shuffle_questions">
                                                    Shuffle Questions
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="show_correct_answers" 
                                                       name="show_correct_answers" {{ old('show_correct_answers', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="show_correct_answers">
                                                    Show Correct Answers
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="show_results_immediately" 
                                                       name="show_results_immediately" {{ old('show_results_immediately', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="show_results_immediately">
                                                    Show Results Immediately
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Date Restrictions</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="start_date" class="form-label">Start Date</label>
                                            <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                                   id="start_date" name="start_date" value="{{ old('start_date') }}">
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="end_date" class="form-label">End Date</label>
                                            <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                                   id="end_date" name="end_date" value="{{ old('end_date') }}">
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('instructor.quizzes.index') }}" class="btn btn-secondary">
                                        <i class="bx bx-arrow-back"></i> Back to Quizzes
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-save"></i> Create Quiz
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Date validation
    document.getElementById('start_date').addEventListener('change', function() {
        const startDate = this.value;
        const endDateInput = document.getElementById('end_date');
        
        if (startDate && endDateInput.value && startDate >= endDateInput.value) {
            endDateInput.setCustomValidity('End date must be after start date');
        } else {
            endDateInput.setCustomValidity('');
        }
    });

    document.getElementById('end_date').addEventListener('change', function() {
        const endDate = this.value;
        const startDateInput = document.getElementById('start_date');
        
        if (endDate && startDateInput.value && startDateInput.value >= endDate) {
            this.setCustomValidity('End date must be after start date');
        } else {
            this.setCustomValidity('');
        }
    });
</script>
@endpush 