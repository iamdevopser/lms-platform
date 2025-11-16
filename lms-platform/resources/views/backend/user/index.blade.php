@extends('backend.user.master')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Notifications -->
    @if(request('subscription') == 'success')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Success!</strong> Your subscription has been successfully activated!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(request('subscription') == 'cancel')
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Notice:</strong> Your subscription process was cancelled.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1">Welcome back, {{ Auth::user()->name }}!</h4>
                            <p class="mb-0">Here's what's happening with your account today.</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <i class="fas fa-user-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription Status -->
    @php
        $activeSubscription = auth()->user()->subscriptions()->where('status', 'active')->first();
        $expiredSubscriptions = auth()->user()->subscriptions()->where('status', 'canceled')->get();
    @endphp
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-credit-card me-2"></i>
                        Subscription Status
                    </h5>
                </div>
                <div class="card-body">
                    @if($activeSubscription)
                        <div class="alert alert-success">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="mb-1">
                                        <i class="fas fa-check-circle me-2"></i>
                                        Active Subscription: <strong>{{ $activeSubscription->subscriptionPlan->name ?? 'Premium Plan' }}</strong>
                                    </h6>
                                    <p class="mb-0 text-muted">
                                        Expires: {{ $activeSubscription->current_period_end ? $activeSubscription->current_period_end->format('F j, Y') : 'N/A' }}
                                        @if($activeSubscription->current_period_end && $activeSubscription->current_period_end->diffInDays(now()) <= 7)
                                            <span class="badge bg-warning ms-2">Expires Soon</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="{{ route('pricing') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-arrow-up me-1"></i> Upgrade
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="mb-1">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No Active Subscription
                                    </h6>
                                    <p class="mb-0 text-muted">Subscribe to unlock premium features and unlimited access.</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="{{ route('pricing') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i> Subscribe Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Courses</h6>
                            <h3 class="mb-0">0</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-book fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Completed</h6>
                            <h3 class="mb-0">0</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">In Progress</h6>
                            <h3 class="mb-0">0</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Certificates</h6>
                            <h3 class="mb-0">0</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-certificate fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>
                        Recent Activity
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>No recent activity</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bell me-2"></i>
                        Notifications
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-bell-slash fa-3x mb-3"></i>
                        <p>No new notifications</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@endsection



