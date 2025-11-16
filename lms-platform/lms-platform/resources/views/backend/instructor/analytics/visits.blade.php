@extends('backend.instructor.master')

@section('content')
<div class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Analytics</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Visits Analytics</li>
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
                            <h6 class="mb-0">Visits Overview</h6>
                        </div>
                        <div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                                <i class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('instructor.analytics.visits.excel', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}">Export to Excel</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#emailReportModal" data-report-type="visits">Send Email Report</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center ms-auto font-13 gap-2 mb-3">
                        <span class="border px-1 rounded cursor-pointer">
                            <i class="bx bxs-circle me-1" style="color: #14abef"></i>Views
                        </span>
                        <span class="border px-1 rounded cursor-pointer">
                            <i class="bx bxs-circle me-1" style="color: #ffc107"></i>Unique Visitors
                        </span>
                    </div>
                    <div class="chart-container-1">
                        <canvas id="visitsChart"></canvas>
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
                            <i class='bx bxs-show'></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Total Views</h6>
                            <h4 class="mb-0">{{ number_format($totalViews) }}</h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white">
                            <i class='bx bxs-user'></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Unique Visitors</h6>
                            <h4 class="mb-0">{{ number_format($visits->sum('unique_visitors')) }}</h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white">
                            <i class='bx bxs-mouse'></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Total Clicks</h6>
                            <h4 class="mb-0">{{ number_format($visits->sum('clicks')) }}</h4>
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
                            <h6 class="mb-0">Visits Details</h6>
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
                                    <th>Views</th>
                                    <th>Unique Visitors</th>
                                    <th>Clicks</th>
                                    <th>Avg Watch Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visits as $visit)
                                <tr>
                                    <td>{{ $visit->date->format('M d, Y') }}</td>
                                    <td>{{ $visit->course ? $visit->course->course_title : 'N/A' }}</td>
                                    <td>{{ number_format($visit->views) }}</td>
                                    <td>{{ number_format($visit->unique_visitors) }}</td>
                                    <td>{{ number_format($visit->clicks) }}</td>
                                    <td>{{ gmdate('H:i:s', $visit->avg_watch_time) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No visits data available</td>
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
    const ctx = document.getElementById('visitsChart').getContext('2d');
    
    const viewsData = @json($visits->pluck('views'));
    const uniqueVisitorsData = @json($visits->pluck('unique_visitors'));
    const labels = @json($visits->pluck('date')->map(function($date) { return $date->format('M d'); }));
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Views',
                data: viewsData,
                borderColor: '#14abef',
                backgroundColor: 'rgba(20, 171, 239, 0.1)',
                tension: 0.4,
                fill: false
            }, {
                label: 'Unique Visitors',
                data: uniqueVisitorsData,
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.4,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
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
