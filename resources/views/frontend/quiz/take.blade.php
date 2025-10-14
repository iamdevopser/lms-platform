@extends('frontend.master')

@section('title', 'Taking Quiz: ' . $quiz->title)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Quiz Header -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $quiz->title }}</h4>
                            <small>{{ $quiz->course->title }}</small>
                        </div>
                        <div class="text-end">
                            @if($quiz->time_limit)
                                <div class="timer-display">
                                    <i class="bx bx-time"></i>
                                    <span id="timer" class="h5 mb-0">{{ $quiz->time_limit }}:00</span>
                                </div>
                            @endif
                            <small>Question {{ $attempt->answers->count() + 1 }} of {{ $questions->count() }}</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-0">{{ $quiz->description }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="quiz-info">
                                <span class="badge bg-info me-2">{{ ucfirst($quiz->type) }}</span>
                                <span class="badge bg-secondary me-2">{{ $quiz->question_count }} Questions</span>
                                <span class="badge bg-warning">{{ $quiz->total_points }} Points</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiz Form -->
            <form id="quizForm" action="{{ route('student.quizzes.submit', $attempt->id) }}" method="POST">
                @csrf
                
                <!-- Progress Bar -->
                <div class="progress mb-4" style="height: 10px;">
                    <div class="progress-bar bg-primary" role="progressbar" 
                         style="width: {{ ($attempt->answers->count() / $questions->count()) * 100 }}%">
                    </div>
                </div>

                <!-- Questions -->
                @foreach($questions as $index => $question)
                <div class="card mb-4 question-card" id="question-{{ $question->id }}">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Question {{ $index + 1 }}</h5>
                            <span class="badge bg-primary">{{ $question->points }} point{{ $question->points > 1 ? 's' : '' }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="question-content mb-4">
                            <p class="h6">{{ $question->question }}</p>
                        </div>

                        <div class="question-options">
                            @switch($question->type)
                                @case('multiple_choice')
                                    @foreach($question->options as $key => $option)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" 
                                               name="answers[{{ $question->id }}][]" 
                                               value="{{ $key }}" 
                                               id="option-{{ $question->id }}-{{ $key }}">
                                        <label class="form-check-label" for="option-{{ $question->id }}-{{ $key }}">
                                            {{ $option }}
                                        </label>
                                    </div>
                                    @endforeach
                                    @break

                                @case('single_choice')
                                    @foreach($question->options as $key => $option)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               value="{{ $key }}" 
                                               id="option-{{ $question->id }}-{{ $key }}">
                                        <label class="form-check-label" for="option-{{ $question->id }}-{{ $key }}">
                                            {{ $option }}
                                        </label>
                                    </div>
                                    @endforeach
                                    @break

                                @case('true_false')
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               value="true" 
                                               id="option-{{ $question->id }}-true">
                                        <label class="form-check-label" for="option-{{ $question->id }}-true">
                                            True
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               value="false" 
                                               id="option-{{ $question->id }}-false">
                                        <label class="form-check-label" for="option-{{ $question->id }}-false">
                                            False
                                        </label>
                                    </div>
                                    @break

                                @case('fill_blank')
                                    <div class="form-group">
                                        <input type="text" class="form-control" 
                                               name="answers[{{ $question->id }}][]" 
                                               placeholder="Enter your answer">
                                    </div>
                                    @break

                                @case('essay')
                                    <div class="form-group">
                                        <textarea class="form-control" rows="4" 
                                                  name="answers[{{ $question->id }}][]" 
                                                  placeholder="Write your answer here..."></textarea>
                                    </div>
                                    @break

                                @default
                                    <p class="text-muted">Question type not supported.</p>
                            @endswitch
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Quiz Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button type="button" class="btn btn-secondary" id="saveProgress">
                                    <i class="bx bx-save"></i> Save Progress
                                </button>
                                <button type="button" class="btn btn-warning" id="abandonQuiz">
                                    <i class="bx bx-x"></i> Abandon Quiz
                                </button>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-success btn-lg" id="submitQuiz">
                                    <i class="bx bx-check"></i> Submit Quiz
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Quiz Sidebar -->
        <div class="col-lg-4">
            <div class="position-sticky" style="top: 2rem;">
                <!-- Question Navigator -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Question Navigator</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2" id="questionNavigator">
                            @foreach($questions as $index => $question)
                            <div class="col-3">
                                <button type="button" class="btn btn-outline-primary btn-sm w-100 question-nav-btn" 
                                        data-question="{{ $question->id }}" 
                                        data-index="{{ $index }}">
                                    {{ $index + 1 }}
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Quiz Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Quiz Information</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bx bx-book-open text-primary"></i>
                                <strong>Course:</strong> {{ $quiz->course->title }}
                            </li>
                            <li class="mb-2">
                                <i class="bx bx-time text-warning"></i>
                                <strong>Time Limit:</strong> 
                                {{ $quiz->time_limit ? $quiz->time_limit . ' minutes' : 'No limit' }}
                            </li>
                            <li class="mb-2">
                                <i class="bx bx-target-lock text-info"></i>
                                <strong>Passing Score:</strong> {{ $quiz->passing_score }}%
                            </li>
                            <li class="mb-2">
                                <i class="bx bx-refresh text-secondary"></i>
                                <strong>Attempt:</strong> {{ $attempt->attempt_number }} of {{ $quiz->max_attempts }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Progress Summary -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Progress Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <div class="progress-circle mb-3">
                                <div class="progress-circle-inner">
                                    <span class="progress-percentage">{{ round(($attempt->answers->count() / $questions->count()) * 100) }}%</span>
                                </div>
                            </div>
                            <p class="mb-1"><strong>{{ $attempt->answers->count() }}</strong> of <strong>{{ $questions->count() }}</strong> questions answered</p>
                            <small class="text-muted">{{ $questions->count() - $attempt->answers->count() }} questions remaining</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Abandon Quiz Modal -->
<div class="modal fade" id="abandonModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Abandon Quiz?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to abandon this quiz? Your progress will be lost and this will count as an attempt.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('student.quizzes.abandon', $attempt->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">Yes, Abandon Quiz</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let timeLeft = {{ $quiz->time_limit ? $quiz->time_limit * 60 : 0 }};
    let timer;

    // Initialize timer if time limit exists
    if (timeLeft > 0) {
        timer = setInterval(function() {
            timeLeft--;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            
            $('#timer').text(`${minutes}:${seconds.toString().padStart(2, '0')}`);
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                $('#quizForm').submit();
            }
        }, 1000);
    }

    // Question navigation
    $('.question-nav-btn').click(function() {
        const questionId = $(this).data('question');
        $('html, body').animate({
            scrollTop: $(`#question-${questionId}`).offset().top - 100
        }, 500);
    });

    // Save progress (auto-save every 30 seconds)
    setInterval(function() {
        // Save answers to localStorage
        const answers = {};
        $('input, textarea').each(function() {
            const name = $(this).attr('name');
            if (name && name.startsWith('answers[')) {
                if ($(this).attr('type') === 'checkbox') {
                    if (!answers[name]) answers[name] = [];
                    if ($(this).is(':checked')) {
                        answers[name].push($(this).val());
                    }
                } else {
                    answers[name] = $(this).val();
                }
            }
        });
        localStorage.setItem('quiz_answers_{{ $attempt->id }}', JSON.stringify(answers));
    }, 30000);

    // Load saved answers
    const savedAnswers = localStorage.getItem('quiz_answers_{{ $attempt->id }}');
    if (savedAnswers) {
        const answers = JSON.parse(savedAnswers);
        Object.keys(answers).forEach(name => {
            const value = answers[name];
            const element = $(`[name="${name}"]`);
            
            if (Array.isArray(value)) {
                value.forEach(val => {
                    $(`[name="${name}"][value="${val}"]`).prop('checked', true);
                });
            } else {
                element.val(value);
            }
        });
    }

    // Abandon quiz confirmation
    $('#abandonQuiz').click(function() {
        $('#abandonModal').modal('show');
    });

    // Form submission confirmation
    $('#submitQuiz').click(function(e) {
        const answeredCount = $('input:checked, textarea:not(:empty)').length;
        const totalQuestions = {{ $questions->count() }};
        
        if (answeredCount < totalQuestions) {
            if (!confirm(`You have only answered ${answeredCount} out of ${totalQuestions} questions. Are you sure you want to submit?`)) {
                e.preventDefault();
            }
        }
    });

    // Update question navigator
    function updateQuestionNavigator() {
        $('.question-nav-btn').each(function() {
            const questionId = $(this).data('question');
            const hasAnswer = $(`[name^="answers[${questionId}]"]:checked, [name^="answers[${questionId}]"]:not(:empty)`).length > 0;
            
            if (hasAnswer) {
                $(this).removeClass('btn-outline-primary').addClass('btn-success');
            }
        });
    }

    // Update navigator on input change
    $('input, textarea').on('change input', updateQuestionNavigator);
    updateQuestionNavigator();
});
</script>
@endpush

@push('styles')
<style>
.question-card {
    border-left: 4px solid #007bff;
}

.question-nav-btn {
    min-width: 40px;
}

.progress-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: conic-gradient(#007bff 0deg, #007bff {{ ($attempt->answers->count() / $questions->count()) * 360 }}deg, #e9ecef {{ ($attempt->answers->count() / $questions->count()) * 360 }}deg, #e9ecef 360deg);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.progress-circle-inner {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
}

.progress-percentage {
    font-size: 1.2rem;
    font-weight: bold;
    color: #007bff;
}

.timer-display {
    background: rgba(255,255,255,0.2);
    padding: 8px 16px;
    border-radius: 20px;
}

.timer-display #timer {
    font-family: 'Courier New', monospace;
    font-weight: bold;
}

.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.form-check-label {
    cursor: pointer;
    user-select: none;
}

.question-content {
    font-size: 1.1rem;
    line-height: 1.6;
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