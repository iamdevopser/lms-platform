@extends('backend.instructor.master')

@section('title', 'Quiz Details')

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
                        <li class="breadcrumb-item active">{{ $quiz->title }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Quiz Details -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Quiz Details</h5>
                            <div>
                                <a href="{{ route('instructor.quizzes.edit', $quiz->id) }}" class="btn btn-primary btn-sm">
                                    <i class="bx bx-edit"></i> Edit Quiz
                                </a>
                                <a href="{{ route('instructor.quizzes.questions.create', $quiz->id) }}" class="btn btn-success btn-sm">
                                    <i class="bx bx-plus"></i> Add Question
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4>{{ $quiz->title }}</h4>
                                <p class="text-muted">{{ $quiz->description }}</p>
                                
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <strong>Course:</strong> {{ $quiz->course->title }}
                                        </div>
                                        <div class="info-item">
                                            <strong>Type:</strong> 
                                            <span class="badge bg-{{ $quiz->type === 'quiz' ? 'primary' : ($quiz->type === 'exam' ? 'warning' : 'info') }}">
                                                {{ ucfirst($quiz->type) }}
                                            </span>
                                        </div>
                                        <div class="info-item">
                                            <strong>Status:</strong> 
                                            <span class="badge bg-{{ $quiz->is_active ? 'success' : 'danger' }}">
                                                {{ $quiz->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <strong>Time Limit:</strong> 
                                            {{ $quiz->time_limit ? $quiz->time_limit . ' minutes' : 'No limit' }}
                                        </div>
                                        <div class="info-item">
                                            <strong>Passing Score:</strong> {{ $quiz->passing_score }}%
                                        </div>
                                        <div class="info-item">
                                            <strong>Max Attempts:</strong> {{ $quiz->max_attempts }}
                                        </div>
                                    </div>
                                </div>

                                @if($quiz->start_date || $quiz->end_date)
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        @if($quiz->start_date)
                                        <div class="info-item">
                                            <strong>Start Date:</strong> {{ $quiz->start_date->format('M d, Y H:i') }}
                                        </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        @if($quiz->end_date)
                                        <div class="info-item">
                                            <strong>End Date:</strong> {{ $quiz->end_date->format('M d, Y H:i') }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <strong>Questions:</strong> {{ $quiz->questions->count() }}
                                        </div>
                                        <div class="info-item">
                                            <strong>Total Points:</strong> {{ $quiz->getTotalPointsAttribute() }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <strong>Attempts:</strong> {{ $quiz->attempts->count() }}
                                        </div>
                                        <div class="info-item">
                                            <strong>Average Score:</strong> {{ number_format($quiz->getAverageScoreAttribute(), 1) }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6>Quiz Statistics</h6>
                                        <div class="mt-3">
                                            <div class="stat-item">
                                                <h4>{{ $quiz->attempts->where('status', 'completed')->count() }}</h4>
                                                <small>Completed Attempts</small>
                                            </div>
                                            <div class="stat-item mt-3">
                                                <h4>{{ $quiz->attempts->where('passed', true)->count() }}</h4>
                                                <small>Passed Attempts</small>
                                            </div>
                                            <div class="stat-item mt-3">
                                                <h4>{{ number_format($quiz->getPassRateAttribute(), 1) }}%</h4>
                                                <small>Pass Rate</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Questions ({{ $quiz->questions->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($quiz->questions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Question</th>
                                            <th>Type</th>
                                            <th>Points</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($quiz->questions->sortBy('order') as $question)
                                        <tr>
                                            <td>{{ $question->order }}</td>
                                            <td>{{ Str::limit($question->question, 100) }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $question->type)) }}</span>
                                            </td>
                                            <td>{{ $question->points }}</td>
                                            <td>
                                                <span class="badge bg-{{ $question->is_active ? 'success' : 'danger' }}">
                                                    {{ $question->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('instructor.quizzes.questions.edit', [$quiz->id, $question->id]) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteQuestion({{ $question->id }})">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bx bx-question-mark display-1 text-muted"></i>
                                <h5 class="mt-3">No Questions Added</h5>
                                <p class="text-muted">This quiz doesn't have any questions yet.</p>
                                <a href="{{ route('instructor.quizzes.questions.create', $quiz->id) }}" class="btn btn-primary">
                                    <i class="bx bx-plus"></i> Add First Question
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Attempts -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Attempts</h5>
                    </div>
                    <div class="card-body">
                        @if($attempts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Attempt #</th>
                                            <th>Score</th>
                                            <th>Status</th>
                                            <th>Started</th>
                                            <th>Completed</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($attempts as $attempt)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $attempt->user->profile_photo_url }}" 
                                                         class="rounded-circle me-2" width="32" height="32">
                                                    {{ $attempt->user->name }}
                                                </div>
                                            </td>
                                            <td>{{ $attempt->attempt_number }}</td>
                                            <td>
                                                @if($attempt->isCompleted())
                                                    <span class="badge bg-{{ $attempt->passed ? 'success' : 'danger' }}">
                                                        {{ $attempt->percentage }}%
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $attempt->status === 'completed' ? 'success' : ($attempt->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $attempt->started_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                @if($attempt->completed_at)
                                                    {{ $attempt->completed_at->format('M d, Y H:i') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('instructor.quizzes.attempts.show', [$quiz->id, $attempt->id]) }}" 
                                                   class="btn btn-sm btn-outline-info">
                                                    <i class="bx bx-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-center mt-3">
                                {{ $attempts->links() }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bx bx-user-check display-1 text-muted"></i>
                                <h5 class="mt-3">No Attempts Yet</h5>
                                <p class="text-muted">No students have attempted this quiz yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Question Modal -->
<div class="modal fade" id="deleteQuestionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this question? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteQuestionForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
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
function deleteQuestion(questionId) {
    if (confirm('Are you sure you want to delete this question?')) {
        fetch(`/instructor/quizzes/questions/${questionId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting question: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting question');
        });
    }
}
</script>
@endpush 