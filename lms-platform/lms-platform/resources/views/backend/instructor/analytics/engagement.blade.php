@extends('backend.instructor.master')

@section('content')
<div class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Analytics</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Engagement Analytics</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8 d-flex">
            <div class="card radius-10 w-100">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">Engagement Overview</h6>
                        </div>
                        <div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                                <i class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:;">Export Data</a></li>
                                <li><a class="dropdown-item" href="javascript:;">Print Report</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#emailReportModal" data-report-type="engagement">Send Email Report</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container-1">
                        <canvas id="engagementChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4 d-flex">
            <div class="card radius-10 w-100">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">Summary</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white">
                            <i class='bx bxs-heart'></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Total Engagements</h6>
                            <h4 class="mb-0">{{ number_format($totalEngagements) }}</h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white">
                            <i class='bx bxs-comment'></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Comments</h6>
                            <h4 class="mb-0">{{ number_format($engagements->where('engagement_type', 'comment')->count()) }}</h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white">
                            <i class='bx bxs-check-circle'></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Completions</h6>
                            <h4 class="mb-0">{{ number_format($engagements->where('engagement_type', 'complete')->count()) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card radius-10">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">Engagement Details</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Course</th>
                                    <th>Student</th>
                                    <th>Engagement Type</th>
                                    <th>Value</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($engagements as $engagement)
                                <tr>
                                    <td>{{ $engagement->date->format('M d, Y') }}</td>
                                    <td>{{ $engagement->course ? $engagement->course->course_title : 'N/A' }}</td>
                                    <td>{{ $engagement->user ? $engagement->user->name : 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $engagement->engagement_type == 'comment' ? 'primary' : ($engagement->engagement_type == 'complete' ? 'success' : 'info') }}">
                                            {{ ucfirst($engagement->engagement_type) }}
                                        </span>
                                    </td>
                                    <td>{{ $engagement->engagement_value ?? 'N/A' }}</td>
                                    <td>
                                        @if($engagement->meta)
                                            <small class="text-muted">{{ json_encode($engagement->meta) }}</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No engagement data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Report Modal -->
<div class="modal fade" id="emailReportModal" tabindex="-1" aria-labelledby="emailReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailReportModalLabel">Send Email Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="emailReportForm">
                    <div class="mb-3">
                        <label for="reportType" class="form-label">Report Type</label>
                        <input type="text" class="form-control" id="reportType" name="report_type" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="period" class="form-label">Period</label>
                        <select class="form-select" id="period" name="period" required>
                            <option value="weekly">Last Week</option>
                            <option value="monthly">Last Month</option>
                            <option value="quarterly">Last Quarter</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="sendEmailReport">Send Report</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('engagementChart').getContext('2d');
    
    const engagementTypes = @json($engagements->groupBy('engagement_type')->keys());
    const engagementCounts = @json($engagements->groupBy('engagement_type')->map->count()->values());
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: engagementTypes,
            datasets: [{
                data: engagementCounts,
                backgroundColor: [
                    '#14abef',
                    '#ffc107',
                    '#dc3545',
                    '#28a745',
                    '#6f42c1'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Email Report Modal Functionality
    $('#emailReportModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var reportType = button.data('report-type');
        var modal = $(this);
        modal.find('#reportType').val(reportType);
    });

    $('#sendEmailReport').on('click', function() {
        var formData = $('#emailReportForm').serialize();
        
        $.ajax({
            url: '{{ route("instructor.analytics.email.send") }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#emailReportModal').modal('hide');
                    $('#emailReportForm')[0].reset();
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                if (errors) {
                    Object.keys(errors).forEach(function(key) {
                        alert(errors[key][0]);
                    });
                } else {
                    alert('An error occurred while sending the report.');
                }
            }
        });
    });
});
</script>
@endpush
@endsection
