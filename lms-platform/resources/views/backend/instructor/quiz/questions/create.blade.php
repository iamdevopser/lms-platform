@extends('backend.instructor.master')

@section('title', 'Add Question')

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
                        <li class="breadcrumb-item active">Add Question</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Add Question Form -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Add Question to: {{ $quiz->title }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('instructor.quizzes.questions.store', $quiz->id) }}" method="POST" id="questionForm">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="question" class="form-label">Question Text *</label>
                                        <textarea class="form-control @error('question') is-invalid @enderror" 
                                                  id="question" name="question" rows="4" required>{{ old('question') }}</textarea>
                                        @error('question')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="type" class="form-label">Question Type *</label>
                                                <select class="form-select @error('type') is-invalid @enderror" 
                                                        id="type" name="type" required>
                                                    <option value="">Select Type</option>
                                                    <option value="single_choice" {{ old('type') === 'single_choice' ? 'selected' : '' }}>Single Choice</option>
                                                    <option value="multiple_choice" {{ old('type') === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                                    <option value="true_false" {{ old('type') === 'true_false' ? 'selected' : '' }}>True/False</option>
                                                    <option value="fill_blank" {{ old('type') === 'fill_blank' ? 'selected' : '' }}>Fill in the Blank</option>
                                                    <option value="essay" {{ old('type') === 'essay' ? 'selected' : '' }}>Essay</option>
                                                </select>
                                                @error('type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="points" class="form-label">Points *</label>
                                                <input type="number" class="form-control @error('points') is-invalid @enderror" 
                                                       id="points" name="points" value="{{ old('points', 1) }}" 
                                                       min="1" required>
                                                @error('points')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="order" class="form-label">Question Order</label>
                                        <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                               id="order" name="order" value="{{ old('order', $quiz->questions->count() + 1) }}" 
                                               min="1">
                                        <small class="form-text text-muted">Leave empty to add at the end</small>
                                        @error('order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="explanation" class="form-label">Explanation</label>
                                        <textarea class="form-control @error('explanation') is-invalid @enderror" 
                                                  id="explanation" name="explanation" rows="3">{{ old('explanation') }}</textarea>
                                        <small class="form-text text-muted">Optional explanation for the correct answer</small>
                                        @error('explanation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Options Section (for choice questions) -->
                                    <div id="optionsSection" style="display: none;">
                                        <h6>Answer Options</h6>
                                        <div id="optionsContainer">
                                            <!-- Options will be dynamically added here -->
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="addOption">
                                            <i class="bx bx-plus"></i> Add Option
                                        </button>
                                    </div>

                                    <!-- Correct Answers Section -->
                                    <div id="correctAnswersSection" style="display: none;">
                                        <h6>Correct Answer(s)</h6>
                                        <div id="correctAnswersContainer">
                                            <!-- Correct answers will be dynamically added here -->
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <h6 class="mb-0">Question Preview</h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="questionPreview">
                                                <p class="text-muted">Question preview will appear here...</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card bg-light mt-3">
                                        <div class="card-header">
                                            <h6 class="mb-0">Quiz Info</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="info-item">
                                                <strong>Course:</strong> {{ $quiz->course->title }}
                                            </div>
                                            <div class="info-item">
                                                <strong>Quiz Type:</strong> {{ ucfirst($quiz->type) }}
                                            </div>
                                            <div class="info-item">
                                                <strong>Current Questions:</strong> {{ $quiz->questions->count() }}
                                            </div>
                                            <div class="info-item">
                                                <strong>Total Points:</strong> {{ $quiz->getTotalPointsAttribute() }}
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
                                            <button type="button" class="btn btn-outline-primary" id="previewBtn">
                                                <i class="bx bx-eye"></i> Preview
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bx bx-save"></i> Save Question
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
.info-item {
    margin-bottom: 0.5rem;
}

.option-item {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    background-color: #fff;
}

.option-item .form-check {
    margin-bottom: 0;
}

.remove-option {
    color: #dc3545;
    cursor: pointer;
}

.remove-option:hover {
    color: #c82333;
}
</style>
@endpush

@push('scripts')
<script>
let optionCounter = 0;

// Show/hide options based on question type
document.getElementById('type').addEventListener('change', function() {
    const type = this.value;
    const optionsSection = document.getElementById('optionsSection');
    const correctAnswersSection = document.getElementById('correctAnswersSection');
    
    if (['single_choice', 'multiple_choice', 'true_false'].includes(type)) {
        optionsSection.style.display = 'block';
        correctAnswersSection.style.display = 'block';
        
        if (type === 'true_false') {
            setupTrueFalseOptions();
        } else {
            setupChoiceOptions();
        }
    } else if (type === 'fill_blank') {
        optionsSection.style.display = 'none';
        correctAnswersSection.style.display = 'block';
        setupFillBlankAnswers();
    } else {
        optionsSection.style.display = 'none';
        correctAnswersSection.style.display = 'none';
    }
});

function setupTrueFalseOptions() {
    const container = document.getElementById('optionsContainer');
    container.innerHTML = `
        <div class="option-item">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="options[0]" value="true" checked>
                <label class="form-check-label">True</label>
            </div>
        </div>
        <div class="option-item">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="options[1]" value="false">
                <label class="form-check-label">False</label>
            </div>
        </div>
    `;
    
    setupCorrectAnswers('single_choice');
}

function setupChoiceOptions() {
    const container = document.getElementById('optionsContainer');
    container.innerHTML = '';
    addOption();
    addOption();
    
    const type = document.getElementById('type').value;
    setupCorrectAnswers(type);
}

function setupFillBlankAnswers() {
    const container = document.getElementById('correctAnswersContainer');
    container.innerHTML = `
        <div class="mb-2">
            <label class="form-label">Correct Answer(s)</label>
            <input type="text" class="form-control" name="correct_answers[]" placeholder="Enter correct answer">
            <small class="form-text text-muted">Add multiple answers separated by commas if multiple are acceptable</small>
        </div>
    `;
}

function setupCorrectAnswers(type) {
    const container = document.getElementById('correctAnswersContainer');
    container.innerHTML = '';
    
    if (type === 'multiple_choice') {
        container.innerHTML = `
            <div class="mb-2">
                <label class="form-label">Correct Answer(s)</label>
                <small class="form-text text-muted d-block">Select all correct answers:</small>
                <div id="correctAnswersCheckboxes"></div>
            </div>
        `;
    } else {
        container.innerHTML = `
            <div class="mb-2">
                <label class="form-label">Correct Answer</label>
                <select class="form-select" name="correct_answers[]" required>
                    <option value="">Select correct answer</option>
                </select>
            </div>
        `;
    }
}

function addOption() {
    const container = document.getElementById('optionsContainer');
    const type = document.getElementById('type').value;
    const inputType = type === 'multiple_choice' ? 'checkbox' : 'radio';
    
    const optionDiv = document.createElement('div');
    optionDiv.className = 'option-item';
    optionDiv.innerHTML = `
        <div class="row">
            <div class="col-md-8">
                <div class="form-check">
                    <input class="form-check-input" type="${inputType}" name="options[${optionCounter}]" value="${optionCounter}">
                    <input type="text" class="form-control mt-2" name="option_texts[${optionCounter}]" placeholder="Option text" required>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <span class="remove-option" onclick="removeOption(this)">
                    <i class="bx bx-trash"></i>
                </span>
            </div>
        </div>
    `;
    
    container.appendChild(optionDiv);
    optionCounter++;
    
    updateCorrectAnswersOptions();
}

function removeOption(element) {
    element.closest('.option-item').remove();
    updateCorrectAnswersOptions();
}

function updateCorrectAnswersOptions() {
    const type = document.getElementById('type').value;
    if (type === 'single_choice') {
        const select = document.querySelector('select[name="correct_answers[]"]');
        if (select) {
            select.innerHTML = '<option value="">Select correct answer</option>';
            document.querySelectorAll('input[name^="option_texts"]').forEach((input, index) => {
                const option = document.createElement('option');
                option.value = index;
                option.textContent = input.value || `Option ${index + 1}`;
                select.appendChild(option);
            });
        }
    } else if (type === 'multiple_choice') {
        const container = document.getElementById('correctAnswersCheckboxes');
        if (container) {
            container.innerHTML = '';
            document.querySelectorAll('input[name^="option_texts"]').forEach((input, index) => {
                const div = document.createElement('div');
                div.className = 'form-check';
                div.innerHTML = `
                    <input class="form-check-input" type="checkbox" name="correct_answers[]" value="${index}">
                    <label class="form-check-label">${input.value || `Option ${index + 1}`}</label>
                `;
                container.appendChild(div);
            });
        }
    }
}

// Update correct answers when option texts change
document.addEventListener('input', function(e) {
    if (e.target.name && e.target.name.startsWith('option_texts')) {
        updateCorrectAnswersOptions();
    }
});

// Preview functionality
document.getElementById('previewBtn').addEventListener('click', function() {
    const preview = document.getElementById('questionPreview');
    const question = document.getElementById('question').value;
    const type = document.getElementById('type').value;
    
    if (!question) {
        preview.innerHTML = '<p class="text-muted">Please enter a question first...</p>';
        return;
    }
    
    let previewHtml = `<h6>${question}</h6>`;
    
    if (['single_choice', 'multiple_choice', 'true_false'].includes(type)) {
        const options = document.querySelectorAll('input[name^="option_texts"]');
        if (options.length > 0) {
            previewHtml += '<div class="mt-2">';
            options.forEach((option, index) => {
                const inputType = type === 'multiple_choice' ? 'checkbox' : 'radio';
                previewHtml += `
                    <div class="form-check">
                        <input class="form-check-input" type="${inputType}" disabled>
                        <label class="form-check-label">${option.value || `Option ${index + 1}`}</label>
                    </div>
                `;
            });
            previewHtml += '</div>';
        }
    } else if (type === 'fill_blank') {
        previewHtml += '<div class="mt-2"><input type="text" class="form-control" placeholder="Your answer" disabled></div>';
    } else if (type === 'essay') {
        previewHtml += '<div class="mt-2"><textarea class="form-control" rows="4" placeholder="Your answer" disabled></textarea></div>';
    }
    
    preview.innerHTML = previewHtml;
});

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    const type = document.getElementById('type').value;
    if (type) {
        document.getElementById('type').dispatchEvent(new Event('change'));
    }
});
</script>
@endpush 