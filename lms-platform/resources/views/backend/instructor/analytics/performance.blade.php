@extends('backend.instructor.master')

@section('content')
<div class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Analytics</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Performance Analytics</li>
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
                            <h6 class="mb-0">Performance Overview</h6>
                        </div>
                        <div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                                <i class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:;">Export Data</a></li>
                                <li><a class="dropdown-item" href="javascript:;">Print Report</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container-1">
                        <canvas id="performanceChart"></canvas>
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
                            <i class='bx bxs-wallet'></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Total Earnings</h6>
                            <h4 class="mb-0">${{ number_format($stats['total_earnings'] ?? 0, 2) }}</h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white">
                            <i class='bx bxs-show'></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Total Views</h6>
                            <h4 class="mb-0">{{ number_format($stats['total_views'] ?? 0) }}</h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white">
                            <i class='bx bxs-heart'></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Total Engagements</h6>
                            <h4 class="mb-0">{{ number_format($stats['total_engagements'] ?? 0) }}</h4>
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
                            <h6 class="mb-0">Performance Metrics</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card border-0 shadow-none">
                                <div class="card-body text-center">
                                    <div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white mx-auto mb-3">
                                        <i class='bx bxs-trending-up'></i>
                                    </div>
                                    <h5 class="mb-1">Conversion Rate</h5>
                                    <p class="mb-0 text-success">+2.5%</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-none">
                                <div class="card-body text-center">
                                    <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white mx-auto mb-3">
                                        <i class='bx bxs-time'></i>
                                    </div>
                                    <h5 class="mb-1">Avg Watch Time</h5>
                                    <p class="mb-0 text-info">12:45 min</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-none">
                                <div class="card-body text-center">
                                    <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white mx-auto mb-3">
                                        <i class='bx bxs-star'></i>
                                    </div>
                                    <h5 class="mb-1">Rating</h5>
                                    <p class="mb-0 text-warning">4.8/5.0</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-none">
                                <div class="card-body text-center">
                                    <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white mx-auto mb-3">
                                        <i class='bx bxs-user-check'></i>
                                    </div>
                                    <h5 class="mb-1">Completion Rate</h5>
                                    <p class="mb-0 text-primary">78%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    // Sample data - in real app, this would come from backend
    const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
    const earningsData = [1200, 1900, 3000, 5000, 2000, 3000];
    const viewsData = [100, 200, 300, 400, 500, 600];
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Earnings ($)',
                data: earningsData,
                backgroundColor: 'rgba(20, 171, 239, 0.8)',
                borderColor: '#14abef',
                borderWidth: 1
            }, {
                label: 'Views',
                data: viewsData,
                backgroundColor: 'rgba(255, 193, 7, 0.8)',
                borderColor: '#ffc107',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
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
});
</script>
@endpush
@endsection

