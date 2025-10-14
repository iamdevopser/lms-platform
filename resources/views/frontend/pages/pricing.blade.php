@extends('frontend.master')
@section('title', 'Pricing')
@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="display-4 font-weight-bold">Choose Your Plan</h2>
        <p class="lead text-muted">Select the perfect plan for your learning journey</p>
    </div>
    
    <div class="row justify-content-center">
        @foreach($plans as $plan)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0 {{ $plan->is_popular ? 'border-primary' : '' }}">
                    @if($plan->is_popular)
                        <div class="card-header bg-primary text-white text-center">
                            <span class="badge bg-warning text-dark">Most Popular</span>
                        </div>
                    @endif
                    <div class="card-body text-center p-4">
                        <h5 class="card-title font-weight-bold">{{ $plan->name }}</h5>
                        <div class="display-4 my-3 text-primary">${{ number_format($plan->price, 2) }}</div>
                        <p class="card-text text-muted">{{ $plan->description }}</p>
                        
                        <div class="features-list mb-4">
                            @if(is_array($plan->features))
                                @foreach($plan->features as $feature)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <span>{{ $feature }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        
                        <div class="mb-3">
                            <span class="badge bg-info">{{ ucfirst($plan->billing_cycle) }}</span>
                            @if($plan->trial_days > 0)
                                <span class="badge bg-success">{{ $plan->trial_days }} days trial</span>
                            @endif
                        </div>
                        
                        @if(Auth::check())
                            @if($plan->price > 0)
                                <button type="button" class="btn btn-primary btn-lg w-100 stripe-subscribe-btn" 
                                        data-plan-id="{{ $plan->id }}" 
                                        data-plan-name="{{ $plan->name }}">
                                    <span class="btn-text">Subscribe Now</span>
                                    <span class="btn-loading d-none">
                                        <i class="fas fa-spinner fa-spin"></i> Processing...
                                    </span>
                                </button>
                            @else
                                <form method="POST" action="{{ route('subscribe', $plan) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-lg w-100">
                                        Start Free Trial
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg w-100">
                                Login to Subscribe
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Redirecting to Stripe</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                <p>You are being redirected to Stripe to complete your payment...</p>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Error</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <p id="errorMessage">An error occurred while processing your request.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stripe = Stripe('{{ config('services.stripe.key') }}');
        
        document.querySelectorAll('.stripe-subscribe-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const planId = this.getAttribute('data-plan-id');
                const planName = this.getAttribute('data-plan-name');
                
                // Show loading state
                this.querySelector('.btn-text').classList.add('d-none');
                this.querySelector('.btn-loading').classList.remove('d-none');
                this.disabled = true;
                
                // Show success modal
                $('#successModal').modal('show');
                
                fetch('/stripe/create-subscription', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ plan_id: planId })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success && data.subscription && data.subscription.stripe_checkout_url) {
                        // Redirect to Stripe
                        window.location.href = data.subscription.stripe_checkout_url;
                    } else {
                        throw new Error(data.message || 'Failed to create subscription');
                    }
                })
                .catch(error => {
                    // Hide loading state
                    btn.querySelector('.btn-text').classList.remove('d-none');
                    btn.querySelector('.btn-loading').classList.add('d-none');
                    btn.disabled = false;
                    
                    // Hide success modal
                    $('#successModal').modal('hide');
                    
                    // Show error modal
                    document.getElementById('errorMessage').textContent = error.message;
                    $('#errorModal').modal('show');
                });
            });
        });
    });
</script>
@endsection 