@extends('backend.instructor.master')

@section('title', 'Quiz Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Quiz Management</h4>
                    <a href="{{ route('instructor.quizzes.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus"></i> Create New Quiz
                    </a>
                </div>
                <div class="card-body">
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

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Course</th>
                                    <th>Type</th>
                                    <th>Questions</th>
                                    <th>Attempts</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quizzes as $quiz)
                                <tr>
                                    <td>
                                        <strong>{{ $quiz->title }}</strong>
                                        @if($quiz->description)
                                            <br><small class="text-muted">{{ Str::limit($quiz->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $quiz->course->title }}</td>
                                    <td>
                                        <span class="badge bg-{{ $quiz->type === 'quiz' ? 'primary' : ($quiz->type === 'exam' ? 'warning' : 'info') }}">
                                            {{ ucfirst($quiz->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $quiz->question_count }}</td>
                                    <td>{{ $quiz->attempts->count() }}</td>
                                    <td>
                                        @if($quiz->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $quiz->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('instructor.quizzes.show', $quiz->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            <a href="{{ route('instructor.quizzes.edit', $quiz->id) }}" 
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <a href="{{ route('instructor.quizzes.statistics', $quiz->id) }}" 
                                               class="btn btn-sm btn-outline-info">
                                                <i class="bx bx-bar-chart"></i>
                                            </a>
                                            <form action="{{ route('instructor.quizzes.toggle-status', $quiz->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-{{ $quiz->is_active ? 'secondary' : 'success' }}"
                                                        onclick="return confirm('Are you sure?')">
                                                    <i class="bx bx-{{ $quiz->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            @if($quiz->attempts->count() === 0)
                                            <form action="{{ route('instructor.quizzes.destroy', $quiz->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Are you sure you want to delete this quiz?')">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No quizzes found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $quizzes->links() }}
                    </div>
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
</script>
@endpush 