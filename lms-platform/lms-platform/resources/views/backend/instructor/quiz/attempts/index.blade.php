@extends('backend.instructor.master')

@section('title', 'Quiz Attempts')

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
                        <li class="breadcrumb-item active">Attempts</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Quiz Info -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-2">{{ $quiz->title }}</h5>
                                <p class="text-muted mb-1">{{ $quiz->course->title }}</p>
                                <p class="text-muted mb-0">{{ $quiz->description }}</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="d-flex flex-column align-items-md-end">
                                    <span class="badge bg-{{ $quiz->type === 'quiz' ? 'primary' : ($quiz->type === 'exam' ? 'warning' : 'info') }} fs-6 mb-2">
                                        {{ ucfirst($quiz->type) }}
                                    </span>
                                    <div class="text-muted">
                                        <small>{{ $quiz->questions->count() }} Questions • {{ $quiz->getTotalPointsAttribute() }} Points</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Stats -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <select class="form-select" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="completed">Completed</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="abandoned">Abandoned</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="resultFilter">
                                    <option value="">All Results</option>
                                    <option value="passed">Passed</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" id="searchFilter" placeholder="Search student...">
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary w-100" id="applyFilters">Apply Filters</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h4>{{ $attempts->total() }}</h4>
                        <small>Total Attempts</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attempts List -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Quiz Attempts</h5>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-success btn-sm" onclick="exportAttempts('csv')">
                                    <i class="bx bx-download"></i> Export CSV
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="exportAttempts('pdf')">
                                    <i class="bx bx-download"></i> Export PDF
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($attempts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped" id="attemptsTable">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Attempt #</th>
                                            <th>Started</th>
                                            <th>Completed</th>
                                            <th>Score</th>
                                            <th>Result</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($attempts as $attempt)
                                        <tr class="attempt-row" 
                                            data-status="{{ $attempt->status }}"
                                            data-result="{{ $attempt->passed ? 'passed' : 'failed' }}"
                                            data-student="{{ strtolower($attempt->user->name) }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $attempt->user->profile_photo_url }}" 
                                                         class="rounded-circle me-2" width="32" height="32">
                                                    <div>
                                                        <div class="fw-bold">{{ $attempt->user->name }}</div>
                                                        <small class="text-muted">{{ $attempt->user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $attempt->attempt_number }}</span>
                                            </td>
                                            <td>
                                                <div>{{ $attempt->started_at->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $attempt->started_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                @if($attempt->completed_at)
                                                    <div>{{ $attempt->completed_at->format('M d, Y') }}</div>
                                                    <small class="text-muted">{{ $attempt->completed_at->format('H:i') }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($attempt->isCompleted())
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-{{ $attempt->passed ? 'success' : 'danger' }} me-2">
                                                            {{ $attempt->percentage }}%
                                                        </span>
                                                        <small>{{ $attempt->score }}/{{ $attempt->total_points }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($attempt->isCompleted())
                                                    <span class="badge bg-{{ $attempt->passed ? 'success' : 'danger' }}">
                                                        {{ $attempt->passed ? 'PASSED' : 'FAILED' }}
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
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('instructor.quizzes.attempts.show', [$quiz->id, $attempt->id]) }}" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="bx bx-eye"></i>
                                                    </a>
                                                    @if($attempt->isCompleted())
                                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                onclick="regradeAttempt({{ $attempt->id }})">
                                                            <i class="bx bx-refresh"></i>
                                                        </button>
                                                    @endif
                                                </div>
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
                                <h5 class="mt-3">No Attempts Found</h5>
                                <p class="text-muted">No students have attempted this quiz yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        @if($attempts->count() > 0)
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h4>{{ $attempts->where('passed', true)->count() }}</h4>
                        <small>Passed Attempts</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h4>{{ $attempts->where('passed', false)->where('status', 'completed')->count() }}</h4>
                        <small>Failed Attempts</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h4>{{ $attempts->where('status', 'in_progress')->count() }}</h4>
                        <small>In Progress</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h4>{{ number_format($attempts->where('status', 'completed')->avg('percentage'), 1) }}%</h4>
                        <small>Average Score</small>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Regrade Modal -->
<div class="modal fade" id="regradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Regrade Attempt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to regrade this attempt? This will recalculate the score based on current question settings.</p>
                <div class="alert alert-warning">
                    <i class="bx bx-info-circle"></i>
                    <strong>Note:</strong> Regrading will affect the student's final score and may change their pass/fail status.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="regradeForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning">Regrade Attempt</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.attempt-row {
    transition: all 0.2s ease;
}

.attempt-row:hover {
    background-color: #f8f9fa;
}

.btn-group .btn {
    border-radius: 0.375rem !important;
}

.btn-group .btn:not(:last-child) {
    margin-right: 0.25rem;
}
</style>
@endpush

@push('scripts')
<script>
// Filter functionality
document.getElementById('applyFilters').addEventListener('click', function() {
    const statusFilter = document.getElementById('statusFilter').value;
    const resultFilter = document.getElementById('resultFilter').value;
    const searchFilter = document.getElementById('searchFilter').value.toLowerCase();
    
    const rows = document.querySelectorAll('.attempt-row');
    
    rows.forEach(row => {
        const status = row.dataset.status;
        const result = row.dataset.result;
        const student = row.dataset.student;
        
        let showRow = true;
        
        // Status filter
        if (statusFilter && status !== statusFilter) {
            showRow = false;
        }
        
        // Result filter
        if (resultFilter && result !== resultFilter) {
            showRow = false;
        }
        
        // Search filter
        if (searchFilter && !student.includes(searchFilter)) {
            showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
    });
});

// Export functionality
function exportAttempts(format) {
    const quizId = {{ $quiz->id }};
    const url = `/instructor/quizzes/${quizId}/attempts/export?format=${format}`;
    
    // Create temporary form for download
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    form.appendChild(csrfToken);
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Regrade functionality
function regradeAttempt(attemptId) {
    const modal = new bootstrap.Modal(document.getElementById('regradeModal'));
    const form = document.getElementById('regradeForm');
    
    form.action = `/instructor/quizzes/attempts/${attemptId}/regrade`;
    modal.show();
}

// Auto-apply filters on input change
document.getElementById('searchFilter').addEventListener('input', function() {
    if (this.value.length >= 2 || this.value.length === 0) {
        document.getElementById('applyFilters').click();
    }
});

document.getElementById('statusFilter').addEventListener('change', function() {
    document.getElementById('applyFilters').click();
});

document.getElementById('resultFilter').addEventListener('change', function() {
    document.getElementById('applyFilters').click();
});
</script>
@endpush 